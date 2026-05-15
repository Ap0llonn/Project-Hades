import type { PlasmoCSConfig } from "plasmo"
import type { ContentScriptRequest, ServiceDraft } from "~lib/types"

export const config: PlasmoCSConfig = {
  matches: ["http://*/*", "https://*/*"]
}

const usernameSelectors = [
  'input[autocomplete="username"]',
  'input[autocomplete="email"]',
  'input[type="email"]',
  'input[name*="user" i]',
  'input[name*="email" i]',
  'input[id*="user" i]',
  'input[id*="email" i]',
  'input[name*="login" i]',
  'input[id*="login" i]'
]

const passwordSelectors = [
  'input[type="password"]',
  'input[autocomplete="current-password"]',
  'input[autocomplete="new-password"]'
]

const isVisible = (element: HTMLElement) => {
  const style = window.getComputedStyle(element)
  const rect = element.getBoundingClientRect()

  return (
    style.display !== "none" &&
    style.visibility !== "hidden" &&
    rect.width > 0 &&
    rect.height > 0
  )
}

const getVisibleInputs = (selectors: string[]) =>
  selectors
    .flatMap((selector) => Array.from(document.querySelectorAll<HTMLInputElement>(selector)))
    .filter((input, index, collection) => collection.indexOf(input) === index && isVisible(input))

const extractLoginDraft = (): ServiceDraft => {
  const usernameField = getVisibleInputs(usernameSelectors)[0]
  const passwordField = getVisibleInputs(passwordSelectors)[0]
  const host = window.location.hostname
  const fallbackName = host.replace(/^www\./i, "") || "Login"

  return {
    name: document.title?.trim() || fallbackName,
    url: window.location.origin,
    username: usernameField?.value?.trim() ?? "",
    password: passwordField?.value ?? ""
  }
}

chrome.runtime.onMessage.addListener((request: ContentScriptRequest, _sender, sendResponse) => {
  try {
    switch (request.type) {
      case "page:extract-login-draft":
        sendResponse(extractLoginDraft())
        break
      default:
        sendResponse(null)
    }
  } catch {
    sendResponse({
      name: document.title?.trim() || "Login",
      url: window.location.origin,
      username: "",
      password: ""
    } satisfies ServiceDraft)
  }

  return true
})

