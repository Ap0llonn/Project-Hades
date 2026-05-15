import { useEffect, useState } from "react"
import { sendBackgroundMessage } from "~lib/runtime"
import type { ActiveTabInfo, ServiceDraft, SessionState } from "~lib/types"

const colors = {
  bg: "#f8fafc",
  surface: "#ffffff",
  surfaceMuted: "#f1f5f9",
  border: "#cbd5e1",
  text: "#111827",
  textMuted: "#4b5563",
  primary: "#2563eb",
  onPrimary: "#ffffff"
}

const shellStyle: React.CSSProperties = {
  width: 420,
  minHeight: 560,
  padding: 16,
  boxSizing: "border-box",
  fontFamily: "'DM Sans', 'Segoe UI', sans-serif",
  background: colors.bg,
  color: colors.text
}

const sectionStyle: React.CSSProperties = {
  display: "grid",
  gap: 12,
  padding: 14,
  borderRadius: 12,
  background: colors.surface,
  border: `1px solid ${colors.border}`
}

const labelStyle: React.CSSProperties = {
  fontSize: 13,
  fontWeight: 600,
  color: colors.textMuted
}

const inputStyle: React.CSSProperties = {
  width: "100%",
  borderRadius: 10,
  border: `1px solid ${colors.border}`,
  background: colors.surface,
  color: colors.text,
  padding: "10px 12px",
  fontSize: 14,
  boxSizing: "border-box"
}

const buttonBase: React.CSSProperties = {
  border: 0,
  borderRadius: 10,
  padding: "10px 12px",
  fontSize: 14,
  fontWeight: 600,
  cursor: "pointer"
}

const buildDefaultDraft = (tab: ActiveTabInfo | null): ServiceDraft => {
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

function IndexPopup() {
  const [session, setSession] = useState<SessionState | null>(null)
  const [tab, setTab] = useState<ActiveTabInfo | null>(null)
  const [draft, setDraft] = useState<ServiceDraft>({
    name: "",
    url: "",
    username: "",
    password: ""
  })
  const [busy, setBusy] = useState<string | null>(null)
  const [status, setStatus] = useState("Ready.")

  const runAction = async (label: string, task: () => Promise<void>) => {
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

  const onInput =
    (field: keyof ServiceDraft) => (event: React.ChangeEvent<HTMLInputElement>) => {
      const value = event.target.value
      setDraft((current) => ({
        ...current,
        [field]: value
      }))
    }

  const handleAuth = () =>
    runAction("auth", async () => {
      const nextSession = await sendBackgroundMessage<SessionState>({
        type: "auth:start",
        forcePrompt: false
      })
      setSession(nextSession)
      await verifySession()
      setStatus("Authenticated and verified with backend.")
    })

  const handleExtractFromPage = () =>
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

  const handleSaveService = () =>
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

  return (
    <main style={shellStyle}>
      <div style={{ display: "grid", gap: 12 }}>
        <section style={sectionStyle}>
          <h1 style={{ margin: 0, fontSize: 20, lineHeight: 1.15 }}>VaultGuardian</h1>
          <div style={{ display: "grid", gap: 8 }}>
            {!session?.isAuthenticated || !!session?.isLocked ? (
              <button
                type="button"
                disabled={busy === "auth"}
                onClick={() => void handleAuth()}
                style={{
                  ...buttonBase,
                  background: colors.primary,
                  color: colors.onPrimary
                }}>
                Login
              </button>
            ) : (
              <div style={{ fontSize: 14, color: colors.textMuted }}>
                {session.user?.email ?? "Authenticated"}
              </div>
            )}
          </div>
        </section>

        <section style={sectionStyle}>
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline", gap: 8 }}>
            <h2 style={{ margin: 0, fontSize: 17 }}>Save Service</h2>
            <button
              type="button"
              disabled={busy === "extract"}
              onClick={() => void handleExtractFromPage()}
              style={{
                ...buttonBase,
                padding: "8px 10px",
                fontSize: 12,
                background: colors.surfaceMuted,
                color: colors.text
              }}>
              Read from page
            </button>
          </div>

          <div style={{ display: "grid", gap: 10 }}>
            <label style={{ display: "grid", gap: 6 }}>
              <span style={labelStyle}>Item Name</span>
              <input value={draft.name} onChange={onInput("name")} style={inputStyle} />
            </label>

            <label style={{ display: "grid", gap: 6 }}>
              <span style={labelStyle}>Website URL</span>
              <input value={draft.url} onChange={onInput("url")} style={inputStyle} />
            </label>

            <label style={{ display: "grid", gap: 6 }}>
              <span style={labelStyle}>Username / Email</span>
              <input value={draft.username} onChange={onInput("username")} style={inputStyle} />
            </label>

            <label style={{ display: "grid", gap: 6 }}>
              <span style={labelStyle}>Password</span>
              <input type="password" value={draft.password} onChange={onInput("password")} style={inputStyle} />
            </label>
          </div>

          <button
            type="button"
            disabled={busy === "save" || !session?.isAuthenticated || !!session?.isLocked}
            onClick={() => void handleSaveService()}
            style={{
              ...buttonBase,
              background: colors.primary,
              color: colors.onPrimary
            }}>
            Save Service
          </button>

          <div style={{ fontSize: 12, color: colors.textMuted }}>
            Account: {session?.user?.email ?? "not verified"}
          </div>
          <div style={{ fontSize: 12, color: colors.textMuted }}>
            Active tab: {tab?.url ?? "Open a regular website tab."}
          </div>
        </section>

        <div style={{ minHeight: 18, fontSize: 13, color: status === "Ready." ? colors.textMuted : colors.text }}>
          {status}
        </div>
      </div>
    </main>
  )
}

export default IndexPopup
