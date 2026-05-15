import { saveService } from "~lib/api"
import { ensureDefaultSettings, getSettings, setSettings } from "~lib/storage"
import { getSessionState, lockSession, logout, requireSensitiveAction, startAuthentication, verifySession } from "~lib/auth"
import type {
  ActiveTabInfo,
  BackgroundRequest,
  BackgroundResponse,
  ContentScriptRequest,
  ServiceDraft
} from "~lib/types"

const queryActiveTab = () =>
  new Promise<chrome.tabs.Tab>((resolve, reject) => {
    chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      const activeTab = tabs[0]

      if (!activeTab?.id || !activeTab.url?.startsWith("http")) {
        reject(new Error("Open a regular website tab to use this action."))
        return
      }

      resolve(activeTab)
    })
  })

const sendMessageToActiveTab = async <T>(message: ContentScriptRequest) => {
  const activeTab = await queryActiveTab()

  return new Promise<T>((resolve, reject) => {
    chrome.tabs.sendMessage(activeTab.id!, message, (response: T) => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      resolve(response)
    })
  })
}

const getActiveTabInfo = async (): Promise<ActiveTabInfo> => {
  const tab = await queryActiveTab()
  return {
    url: tab.url ?? "",
    title: tab.title ?? ""
  }
}

const extractLoginDraft = () =>
  sendMessageToActiveTab<ServiceDraft>({
    type: "page:extract-login-draft"
  })

const toResponse = async <T>(task: Promise<T>): Promise<BackgroundResponse<T>> => {
  try {
    return {
      ok: true,
      data: await task
    }
  } catch (error) {
    return {
      ok: false,
      error: error instanceof Error ? error.message : "Unknown extension error."
    }
  }
}

const handleRequest = (request: BackgroundRequest) => {
  switch (request.type) {
    case "session:get-state":
      return toResponse(getSessionState())
    case "session:verify":
      return toResponse(verifySession())
    case "session:lock":
      return toResponse(lockSession("manual"))
    case "auth:start":
      return toResponse(startAuthentication(request.forcePrompt ?? false))
    case "auth:logout":
      return toResponse(logout())
    case "page:get-active-tab":
      return toResponse(getActiveTabInfo())
    case "page:extract-login-draft":
      return toResponse(extractLoginDraft())
    case "vault:save-service":
      return toResponse(saveService(request.draft))
    case "settings:get":
      return toResponse(getSettings())
    case "settings:update":
      return toResponse(
        (async () => {
          await requireSensitiveAction()
          await setSettings(request.settings)
          return getSettings()
        })()
      )
    default:
      return toResponse(Promise.reject(new Error("Unsupported request.")))
  }
}

chrome.runtime.onInstalled.addListener(() => {
  void ensureDefaultSettings()
})

chrome.runtime.onStartup.addListener(() => {
  void ensureDefaultSettings()
})

chrome.runtime.onMessage.addListener((request: BackgroundRequest, _sender, sendResponse) => {
  void handleRequest(request).then(sendResponse)
  return true
})
