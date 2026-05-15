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

export const saveService = async (draft: ServiceDraft): Promise<SaveServiceResult> => {
  const response = await authorizedFetch("/api/extension/services", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      name: draft.name,
      username: draft.username,
      password: draft.password,
      url: draft.url
    })
  })

  const payload = (await response.json().catch(() => null)) as
    | { id?: string; message?: string; status?: string; data?: { id?: string } }
    | null

  return {
    id: payload?.id ?? payload?.data?.id,
    status: payload?.message ?? payload?.status ?? "Credential saved to vault."
  }
}
