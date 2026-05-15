import { getValidAccessToken, refreshAccessToken } from "~lib/auth"
import { getSettings } from "~lib/storage"
import type { SaveServiceResult, ServiceDraft } from "~lib/types"

const extractErrorMessage = async (response: Response) => {
  const body = await response.text()
  return body || `Request failed with status ${response.status}.`
}

const authorizedFetch = async (
  path: string,
  init?: RequestInit,
  retryOnUnauthorized = true
) => {
  const [settings, accessToken] = await Promise.all([
    getSettings(),
    getValidAccessToken()
  ])

  const response = await fetch(new URL(path, settings.backendBaseUrl), {
    ...init,
    credentials: "omit",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${accessToken}`,
      ...(init?.headers ?? {})
    }
  })

  if (response.status === 401 && retryOnUnauthorized) {
    await refreshAccessToken(true)
    return authorizedFetch(path, init, false)
  }

  if (!response.ok) {
    throw new Error(await extractErrorMessage(response))
  }

  return response
}

export const saveService = async (_draft: ServiceDraft): Promise<SaveServiceResult> => {
  throw new Error(
    "Secure extension save is temporarily disabled. Use the web dashboard to save new vault items."
  )
}
