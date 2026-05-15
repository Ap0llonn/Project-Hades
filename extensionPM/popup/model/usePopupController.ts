import { useEffect, useMemo, useState } from "react"
import { sendBackgroundMessage } from "~lib/runtime"
import type { ActiveTabInfo, ServiceDraft, SessionState } from "~lib/types"
import { buildDefaultDraft } from "~popup/model/buildDefaultDraft"

type BusyState = "auth" | "extract" | "save" | null

const defaultDraft: ServiceDraft = {
  name: "",
  url: "",
  username: "",
  password: ""
}

type LoginDraft = {
  email: string
  password: string
}

export const usePopupController = () => {
  const [session, setSession] = useState<SessionState | null>(null)
  const [tab, setTab] = useState<ActiveTabInfo | null>(null)
  const [search, setSearch] = useState("")
  const [draft, setDraft] = useState<ServiceDraft>(defaultDraft)
  const [loginDraft, setLoginDraft] = useState<LoginDraft>({
    email: "",
    password: ""
  })
  const [busy, setBusy] = useState<BusyState>(null)
  const [status, setStatus] = useState("")

  const runAction = async (label: Exclude<BusyState, null>, task: () => Promise<void>) => {
    setBusy(label)
    setStatus("")

    try {
      await task()
    } catch (error) {
      setStatus(error instanceof Error ? error.message : "Unexpected extension error.")
    } finally {
      setBusy(null)
    }
  }

  const refreshSession = async () => {
    const nextSession = await sendBackgroundMessage<SessionState>({
      type: "session:get-state"
    })
    setSession(nextSession)
    return nextSession
  }

  const verifySession = async () => {
    const verified = await sendBackgroundMessage<SessionState>({
      type: "session:verify"
    })
    setSession(verified)
    return verified
  }

  const refreshTab = async () => {
    const nextTab = await sendBackgroundMessage<ActiveTabInfo>({
      type: "page:get-active-tab"
    })
    setTab(nextTab)
    setDraft((current) => {
      const emptyForm =
        !current.name.trim() &&
        !current.url.trim() &&
        !current.username.trim() &&
        !current.password.trim()

      return emptyForm ? buildDefaultDraft(nextTab) : current
    })
  }

  useEffect(() => {
    void (async () => {
      await refreshSession()
      await verifySession().catch(() => undefined)
      await refreshTab().catch(() => undefined)
    })()
  }, [])

  const onInput = (field: keyof ServiceDraft, value: string) => {
    setDraft((current) => ({
      ...current,
      [field]: value
    }))
  }

  const authenticate = async () =>
    runAction("auth", async () => {
      if (!loginDraft.email.trim() || !loginDraft.password.trim()) {
        throw new Error("Email and password are required.")
      }

      const nextSession = await sendBackgroundMessage<SessionState>({
        type: "auth:start",
        forcePrompt: true
      })
      setSession(nextSession)
      await verifySession()
      setStatus("Authenticated and verified with backend.")
    })

  const extractFromPage = async () =>
    runAction("extract", async () => {
      const extracted = await sendBackgroundMessage<ServiceDraft>({
        type: "page:extract-login-draft"
      })
      setDraft((current) => ({
        ...current,
        name: extracted.name || current.name,
        url: extracted.url || current.url,
        username: extracted.username || current.username,
        password: extracted.password || current.password
      }))
      setStatus("Fields populated from active page.")
    })

  const saveService = async () =>
    runAction("save", async () => {
      if (!session?.isAuthenticated || session.isLocked) {
        throw new Error("Authenticate before saving a service.")
      }

      if (!draft.name.trim() || !draft.username.trim() || !draft.password.trim()) {
        throw new Error("Name, username and password are required.")
      }

      const result = await sendBackgroundMessage<{ status: string }>({
        type: "vault:save-service",
        draft: {
          name: draft.name.trim(),
          url: draft.url.trim(),
          username: draft.username.trim(),
          password: draft.password
        }
      })

      setStatus(result.status)
      setDraft((current) => ({
        ...current,
        password: ""
      }))
    })

  const createAccount = () => {
    const target = (() => {
      try {
        const backend = new URL(
          session?.settings.backendBaseUrl ?? "https://vault.vaultguardian.test"
        )
        const marketingHost = backend.hostname.startsWith("vault.")
          ? backend.hostname.slice("vault.".length)
          : backend.hostname
        backend.hostname = marketingHost
        backend.pathname = "/start-account"
        backend.search = ""
        backend.hash = ""

        return backend.toString()
      } catch {
        return "https://vaultguardian.test/start-account"
      }
    })()

    chrome.tabs.create({
      url: target
    })
  }

  const view = useMemo(() => {
    const isAuthenticated = !!session?.isAuthenticated && !session?.isLocked
    const displayName = session?.user?.name?.trim() || session?.user?.email?.trim() || "Vault"
    const userEmail = session?.user?.email?.trim() || "authenticated@vault"
    const emailInitial = userEmail.slice(0, 1).toUpperCase()
    const canSave = !!draft.name.trim() && !!draft.username.trim() && !!draft.password.trim()
    const matchesSearch =
      !search.trim() ||
      `${draft.name} ${draft.username} ${tab?.title ?? ""}`
        .toLowerCase()
        .includes(search.toLowerCase())

    return {
      isAuthenticated,
      displayName,
      userEmail,
      emailInitial,
      canSave,
      matchesSearch
    }
  }, [draft.name, draft.password, draft.username, search, session, tab?.title])

  return {
    session,
    tab,
    search,
    draft,
    loginDraft,
    busy,
    status,
    view,
    setSearch,
    onInput,
    setLoginEmail: (value: string) =>
      setLoginDraft((current) => ({
        ...current,
        email: value
      })),
    setLoginPassword: (value: string) =>
      setLoginDraft((current) => ({
        ...current,
        password: value
      })),
    actions: {
      authenticate,
      extractFromPage,
      saveService,
      createAccount
    }
  }
}
