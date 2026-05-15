import {
  DEFAULT_SETTINGS,
  EMPTY_SESSION_META,
  STORAGE_KEYS
} from "~lib/constants"
import type {
  EncryptedPayload,
  ExtensionSettings,
  PendingAuthSession,
  SessionMeta
} from "~lib/types"

const getFromArea = <T>(area: chrome.storage.StorageArea, key: string) =>
  new Promise<T | null>((resolve, reject) => {
    area.get([key], (result) => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      resolve((result[key] as T | undefined) ?? null)
    })
  })

const setInArea = (area: chrome.storage.StorageArea, values: Record<string, unknown>) =>
  new Promise<void>((resolve, reject) => {
    area.set(values, () => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      resolve()
    })
  })

const removeFromArea = (area: chrome.storage.StorageArea, key: string) =>
  new Promise<void>((resolve, reject) => {
    area.remove(key, () => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      resolve()
    })
  })

export const ensureDefaultSettings = async () => {
  const existing = await getFromArea<ExtensionSettings>(chrome.storage.local, STORAGE_KEYS.settings)

  if (!existing) {
    await setInArea(chrome.storage.local, {
      [STORAGE_KEYS.settings]: DEFAULT_SETTINGS
    })
  }
}

export const getSettings = async (): Promise<ExtensionSettings> => {
  const stored = await getFromArea<ExtensionSettings>(chrome.storage.local, STORAGE_KEYS.settings)
  return {
    ...DEFAULT_SETTINGS,
    ...stored
  }
}

export const setSettings = async (settings: ExtensionSettings) => {
  await setInArea(chrome.storage.local, {
    [STORAGE_KEYS.settings]: settings
  })
}

export const getSessionMeta = async (): Promise<SessionMeta> => {
  const stored = await getFromArea<SessionMeta>(chrome.storage.local, STORAGE_KEYS.sessionMeta)
  return {
    ...EMPTY_SESSION_META,
    ...stored
  }
}

export const setSessionMeta = async (meta: SessionMeta) => {
  await setInArea(chrome.storage.local, {
    [STORAGE_KEYS.sessionMeta]: meta
  })
}

export const patchSessionMeta = async (partial: Partial<SessionMeta>) => {
  const current = await getSessionMeta()
  await setSessionMeta({
    ...current,
    ...partial
  })
}

export const clearSessionMeta = async () => {
  await setSessionMeta(EMPTY_SESSION_META)
}

export const getPendingAuthSession = () =>
  getFromArea<PendingAuthSession>(chrome.storage.session, STORAGE_KEYS.pendingAuth)

export const setPendingAuthSession = async (value: PendingAuthSession) => {
  await setInArea(chrome.storage.session, {
    [STORAGE_KEYS.pendingAuth]: value
  })
}

export const clearPendingAuthSession = () =>
  removeFromArea(chrome.storage.session, STORAGE_KEYS.pendingAuth)

export const getRefreshTokenCipher = () =>
  getFromArea<EncryptedPayload>(chrome.storage.local, STORAGE_KEYS.refreshToken)

export const setRefreshTokenCipher = async (value: EncryptedPayload) => {
  await setInArea(chrome.storage.local, {
    [STORAGE_KEYS.refreshToken]: value
  })
}

export const clearRefreshTokenCipher = () =>
  removeFromArea(chrome.storage.local, STORAGE_KEYS.refreshToken)

export const getSessionKeyMaterial = () =>
  getFromArea<string>(chrome.storage.session, STORAGE_KEYS.sessionKey)

export const setSessionKeyMaterial = async (value: string) => {
  await setInArea(chrome.storage.session, {
    [STORAGE_KEYS.sessionKey]: value
  })
}

export const clearSessionKeyMaterial = () =>
  removeFromArea(chrome.storage.session, STORAGE_KEYS.sessionKey)
