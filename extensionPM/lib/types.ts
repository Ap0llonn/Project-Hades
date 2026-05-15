export type ExtensionSettings = {
  backendBaseUrl: string
  lockTimeoutMinutes: number
  sensitiveActionWindowSeconds: number
}

export type AuthUser = {
  id?: string
  email?: string
  name?: string
}

export type TokenResponse = {
  access_token: string
  refresh_token?: string
  expires_in: number
  refresh_expires_in?: number
  token_type?: string
  user?: AuthUser
}

export type PendingAuthSession = {
  state: string
  codeVerifier: string
  redirectUri: string
  createdAt: number
  forcePrompt: boolean
}

export type EncryptedPayload = {
  ciphertext: string
  iv: string
  createdAt: number
}

export type SessionMeta = {
  lastAuthAt: number | null
  lastActiveAt: number | null
  lockedAt: number | null
  accessTokenExpiresAt: number | null
  refreshTokenExpiresAt: number | null
  user: AuthUser | null
}

export type SessionState = {
  isAuthenticated: boolean
  isLocked: boolean
  hasRefreshToken: boolean
  canPerformSensitiveAction: boolean
  lastAuthAt: number | null
  lastActiveAt: number | null
  lockedAt: number | null
  settings: ExtensionSettings
  user: AuthUser | null
  redirectUri: string
}

export type ActiveTabInfo = {
  url: string
  title: string
}

export type ServiceDraft = {
  name: string
  url: string
  username: string
  password: string
}

export type SaveServiceResult = {
  id?: string
  status: string
}

export type BackgroundRequest =
  | { type: "session:get-state" }
  | { type: "session:verify" }
  | { type: "session:lock" }
  | { type: "auth:start"; forcePrompt?: boolean }
  | { type: "auth:direct-login"; email: string; password: string }
  | { type: "auth:logout" }
  | { type: "page:get-active-tab" }
  | { type: "page:extract-login-draft" }
  | { type: "vault:save-service"; draft: ServiceDraft }
  | { type: "settings:get" }
  | { type: "settings:update"; settings: ExtensionSettings }

export type BackgroundResponse<T> =
  | {
      ok: true
      data: T
    }
  | {
      ok: false
      error: string
    }

export type ContentScriptRequest =
  | { type: "page:extract-login-draft" }
