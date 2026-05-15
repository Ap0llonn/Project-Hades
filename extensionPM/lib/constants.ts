import type { ExtensionSettings, SessionMeta } from "~lib/types"

export const STORAGE_KEYS = {
  settings: "extension-settings",
  pendingAuth: "pending-auth-session",
  sessionMeta: "session-meta",
  refreshToken: "refresh-token-cipher",
  sessionKey: "session-key-material"
} as const

export const DEFAULT_SETTINGS: ExtensionSettings = {
  backendBaseUrl: "https://vault.vaultguardian.test",
  lockTimeoutMinutes: 10,
  sensitiveActionWindowSeconds: 60
}

export const EMPTY_SESSION_META: SessionMeta = {
  lastAuthAt: null,
  lastActiveAt: null,
  lockedAt: null,
  accessTokenExpiresAt: null,
  refreshTokenExpiresAt: null,
  user: null
}

export const ACCESS_TOKEN_SKEW_MS = 60_000
export const AUTH_PENDING_TTL_MS = 10 * 60_000
