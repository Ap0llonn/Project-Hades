import type { ActiveTabInfo, ServiceDraft } from "~lib/types"

export const buildDefaultDraft = (tab: ActiveTabInfo | null): ServiceDraft => {
  const normalizedTitle = tab?.title?.trim() || ""
  const host = (() => {
    try {
      if (!tab?.url) {
        return ""
      }
      return new URL(tab.url).hostname.replace(/^www\./i, "")
    } catch {
      return ""
    }
  })()

  return {
    name: normalizedTitle || host || "Login",
    url: tab?.url ?? "",
    username: "",
    password: ""
  }
}
