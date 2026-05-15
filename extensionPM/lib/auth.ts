import { ACCESS_TOKEN_SKEW_MS, AUTH_PENDING_TTL_MS } from "~lib/constants"
import { createPkceVerifier, createRandomToken, createSessionKeyMaterial, decryptString, encryptString } from "~lib/crypto"
import {
  clearPendingAuthSession,
  clearRefreshTokenCipher,
  clearSessionKeyMaterial,
  clearSessionMeta,
  getPendingAuthSession,
  getRefreshTokenCipher,
  getSessionKeyMaterial,
  getSessionMeta,
  getSettings,
  patchSessionMeta,
  setPendingAuthSession,
  setRefreshTokenCipher,
  setSessionKeyMaterial
} from "~lib/storage"
import type { SessionState, TokenResponse } from "~lib/types"

type AccessTokenCache = {
  token: string
  expiresAt: number
}

let accessTokenCache: AccessTokenCache | null = null

class AuthError extends Error {}

const launchWebAuthFlow = (url: string) =>
  new Promise<string>((resolve, reject) => {
    chrome.identity.launchWebAuthFlow(
      {
        interactive: true,
        url
      },
      (callbackUrl) => {
        if (chrome.runtime.lastError) {
          reject(new AuthError(chrome.runtime.lastError.message))
          return
        }

        if (!callbackUrl) {
          reject(new AuthError("Authentication finished without a callback URL."))
          return
        }

        resolve(callbackUrl)
      }
    )
  })

const now = () => Date.now()

const getRedirectUri = () => chrome.identity.getRedirectURL("auth")

const buildAuthUrl = async (forcePrompt: boolean) => {
  const settings = await getSettings()
  const redirectUri = getRedirectUri()
  const state = createRandomToken()
  const codeVerifier = createPkceVerifier()

  await setPendingAuthSession({
    state,
    codeVerifier,
    redirectUri,
    createdAt: now(),
    forcePrompt
  })

  const url = new URL("/extension/auth/authorize", settings.backendBaseUrl)
  url.searchParams.set("redirect_uri", redirectUri)
  url.searchParams.set("state", state)

  if (forcePrompt) {
    url.searchParams.set("prompt", "login")
  }

  return url.toString()
}

const parseCallbackCode = (callbackUrl: string) => {
  const url = new URL(callbackUrl)
  const searchParams = new URLSearchParams(url.search)
  const hashParams = new URLSearchParams(url.hash.startsWith("#") ? url.hash.slice(1) : url.hash)

  const code = searchParams.get("code") ?? hashParams.get("code")
  const state = searchParams.get("state") ?? hashParams.get("state")
  const error = searchParams.get("error") ?? hashParams.get("error")

  if (error) {
    throw new AuthError(`Authentication failed: ${error}`)
  }

  if (!code || !state) {
    throw new AuthError("Authentication callback is missing code or state.")
  }

  return {
    code,
    state
  }
}

const saveRefreshToken = async (refreshToken: string) => {
  let sessionKey = await getSessionKeyMaterial()

  if (!sessionKey) {
    sessionKey = createSessionKeyMaterial()
    await setSessionKeyMaterial(sessionKey)
  }

  const payload = await encryptString(sessionKey, refreshToken)
  await setRefreshTokenCipher(payload)
}

const loadRefreshToken = async () => {
  const [payload, sessionKey] = await Promise.all([
    getRefreshTokenCipher(),
    getSessionKeyMaterial()
  ])

  if (!payload || !sessionKey) {
    return null
  }

  try {
    return await decryptString(sessionKey, payload)
  } catch {
    return null
  }
}

const resetAccessTokenCache = () => {
  accessTokenCache = null
}

const clearRuntimeSecrets = async () => {
  resetAccessTokenCache()
  await clearSessionKeyMaterial()
}

const clearLocalAuthState = async () => {
  await Promise.all([
    clearPendingAuthSession(),
    clearRefreshTokenCipher(),
    clearRuntimeSecrets(),
    clearSessionMeta()
  ])
}

const touchActivity = () =>
  patchSessionMeta({
    lastActiveAt: now(),
    lockedAt: null
  })

const updateAccessTokenCache = (token: string, expiresInSeconds: number) => {
  accessTokenCache = {
    token,
    expiresAt: now() + expiresInSeconds * 1000
  }
}

const persistTokens = async (
  tokens: TokenResponse,
  options: {
    interactiveAuth: boolean
    preserveRefreshToken?: string | null
  }
) => {
  updateAccessTokenCache(tokens.access_token, tokens.expires_in)

  const refreshToken = tokens.refresh_token ?? options.preserveRefreshToken

  if (refreshToken) {
    await saveRefreshToken(refreshToken)
  }

  const timestamp = now()

  await patchSessionMeta({
    lastActiveAt: timestamp,
    lockedAt: null,
    lastAuthAt: options.interactiveAuth ? timestamp : (await getSessionMeta()).lastAuthAt,
    accessTokenExpiresAt: accessTokenCache.expiresAt,
    refreshTokenExpiresAt: tokens.refresh_expires_in
      ? timestamp + tokens.refresh_expires_in * 1000
      : (await getSessionMeta()).refreshTokenExpiresAt,
    user: tokens.user ?? (await getSessionMeta()).user
  })
}

const getTokenEndpoint = async (path: string) => {
  const settings = await getSettings()
  return new URL(path, settings.backendBaseUrl).toString()
}

type MeResponse = {
  data?: {
    id?: string
    email?: string
    first_name?: string
    last_name?: string
  }
}

const postJson = async <T>(url: string, payload: Record<string, unknown>) => {
  const response = await fetch(url, {
    method: "POST",
    credentials: "omit",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json"
    },
    body: JSON.stringify(payload)
  })

  if (!response.ok) {
    const text = await response.text()
    throw new AuthError(text || `Request failed with status ${response.status}.`)
  }

  const parsed = (await response.json()) as unknown
  if (parsed && typeof parsed === "object" && "data" in parsed) {
    return (parsed as { data: T }).data
  }

  return parsed as T
}

const exchangeCodeForTokens = async (code: string, codeVerifier: string, redirectUri: string) =>
  postJson<TokenResponse>(await getTokenEndpoint("/api/extension/auth/token"), {
    code,
    code_verifier: codeVerifier,
    redirect_uri: redirectUri
  })

const refreshTokens = async (refreshToken: string) =>
  postJson<TokenResponse>(await getTokenEndpoint("/api/extension/auth/refresh"), {
    refresh_token: refreshToken
  })

const revokeSession = async (accessToken: string) => {
  try {
    await fetch(await getTokenEndpoint("/api/extension/auth/revoke"), {
      method: "POST",
      credentials: "omit",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${accessToken}`
      }
    })
  } catch {
    return
  }
}

const ensurePendingAuthSession = async () => {
  const pending = await getPendingAuthSession()

  if (!pending) {
    throw new AuthError("No pending authentication flow was found.")
  }

  if (now() - pending.createdAt > AUTH_PENDING_TTL_MS) {
    await clearPendingAuthSession()
    throw new AuthError("Authentication flow expired. Start again.")
  }

  return pending
}

const applyIdleLockIfNeeded = async () => {
  const [settings, meta] = await Promise.all([getSettings(), getSessionMeta()])

  if (!meta.lastActiveAt || meta.lockedAt) {
    return
  }

  const idleLimitMs = settings.lockTimeoutMinutes * 60_000

  if (idleLimitMs > 0 && now() - meta.lastActiveAt >= idleLimitMs) {
    await lockSession("timeout")
  }
}

export const lockSession = async (_reason: "manual" | "timeout") => {
  await clearRuntimeSecrets()

  await patchSessionMeta({
    lockedAt: now(),
    accessTokenExpiresAt: null
  })

  return getSessionState()
}

export const startAuthentication = async (forcePrompt = false) => {
  const authUrl = await buildAuthUrl(forcePrompt)
  const callbackUrl = await launchWebAuthFlow(authUrl)
  const pending = await ensurePendingAuthSession()
  const { code, state } = parseCallbackCode(callbackUrl)

  if (state !== pending.state) {
    await clearPendingAuthSession()
    throw new AuthError("Authentication state mismatch.")
  }

  const tokens = await exchangeCodeForTokens(
    code,
    pending.codeVerifier,
    pending.redirectUri
  )

  await clearPendingAuthSession()
  await persistTokens(tokens, {
    interactiveAuth: true
  })

  return getSessionState()
}

export const refreshAccessToken = async (force = false) => {
  await applyIdleLockIfNeeded()

  if (!force && accessTokenCache && accessTokenCache.expiresAt > now() + ACCESS_TOKEN_SKEW_MS) {
    await touchActivity()
    return accessTokenCache.token
  }

  const refreshToken = await loadRefreshToken()

  if (!refreshToken) {
    throw new AuthError("Session is locked or no refresh token is available.")
  }

  try {
    const tokens = await refreshTokens(refreshToken)
    await persistTokens(tokens, {
      interactiveAuth: false,
      preserveRefreshToken: refreshToken
    })

    return accessTokenCache?.token ?? tokens.access_token
  } catch (error) {
    await clearLocalAuthState()
    throw error
  }
}

export const getValidAccessToken = async () => {
  await applyIdleLockIfNeeded()
  return refreshAccessToken(false)
}

export const verifySession = async () => {
  await applyIdleLockIfNeeded()

  const current = await getSessionState()
  if (current.isLocked) {
    return current
  }

  const accessToken = await getValidAccessToken()
  const endpoint = await getTokenEndpoint("/api/extension/auth/me")
  const response = await fetch(endpoint, {
    method: "GET",
    credentials: "omit",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${accessToken}`
    }
  })

  if (response.status === 401) {
    await clearLocalAuthState()
    throw new AuthError("Session expired. Please sign in again.")
  }

  if (!response.ok) {
    const text = await response.text()
    throw new AuthError(text || `Unable to verify session (${response.status}).`)
  }

  const payload = (await response.json()) as MeResponse
  const me = payload?.data ?? {}
  const fullName = `${String(me.first_name ?? "").trim()} ${String(me.last_name ?? "").trim()}`.trim()

  await patchSessionMeta({
    user: {
      id: me.id,
      email: me.email,
      name: fullName || me.email
    },
    lastActiveAt: now()
  })

  return getSessionState()
}

export const requireSensitiveAction = async () => {
  await applyIdleLockIfNeeded()

  const [settings, meta] = await Promise.all([getSettings(), getSessionMeta()])

  if (!meta.lastAuthAt) {
    throw new AuthError("Sensitive action requires re-authentication.")
  }

  const windowMs = settings.sensitiveActionWindowSeconds * 1000

  if (now() - meta.lastAuthAt > windowMs) {
    throw new AuthError("Sensitive action requires re-authentication.")
  }

  await touchActivity()
}

export const logout = async () => {
  if (accessTokenCache?.token) {
    await revokeSession(accessTokenCache.token)
  }

  await clearLocalAuthState()
  return getSessionState()
}

export const getSessionState = async (): Promise<SessionState> => {
  await applyIdleLockIfNeeded()

  const [settings, meta, refreshTokenCipher, sessionKey] = await Promise.all([
    getSettings(),
    getSessionMeta(),
    getRefreshTokenCipher(),
    getSessionKeyMaterial()
  ])

  const hasLiveAccessToken =
    !!accessTokenCache && accessTokenCache.expiresAt > now() + ACCESS_TOKEN_SKEW_MS
  const hasRefreshToken = !!refreshTokenCipher
  const isLocked = !!meta.lockedAt || (hasRefreshToken && !sessionKey)
  const canPerformSensitiveAction =
    !isLocked &&
    !!meta.lastAuthAt &&
    now() - meta.lastAuthAt <= settings.sensitiveActionWindowSeconds * 1000

  return {
    isAuthenticated: !isLocked && (hasLiveAccessToken || (!!sessionKey && hasRefreshToken)),
    isLocked,
    hasRefreshToken,
    canPerformSensitiveAction,
    lastAuthAt: meta.lastAuthAt,
    lastActiveAt: meta.lastActiveAt,
    lockedAt: meta.lockedAt,
    settings,
    user: meta.user,
    redirectUri: getRedirectUri()
  }
}
