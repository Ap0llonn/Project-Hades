import { useEffect, useState } from "react"
import { sendBackgroundMessage } from "~lib/runtime"
import type { ExtensionSettings, SessionState } from "~lib/types"

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

const pageStyle: React.CSSProperties = {
  minHeight: "100vh",
  background: colors.bg,
  color: colors.text,
  fontFamily: "'DM Sans', 'Segoe UI', sans-serif",
  padding: 24,
  boxSizing: "border-box"
}

const wrapperStyle: React.CSSProperties = {
  maxWidth: 760,
  margin: "0 auto",
  display: "grid",
  gap: 12
}

const sectionStyle: React.CSSProperties = {
  display: "grid",
  gap: 12,
  padding: 16,
  borderRadius: 12,
  background: colors.surface,
  border: `1px solid ${colors.border}`
}

const inputStyle: React.CSSProperties = {
  width: "100%",
  borderRadius: 10,
  border: `1px solid ${colors.border}`,
  padding: "10px 12px",
  boxSizing: "border-box",
  fontSize: 14
}

const buttonStyle: React.CSSProperties = {
  border: 0,
  borderRadius: 10,
  padding: "10px 12px",
  fontSize: 14,
  fontWeight: 600,
  cursor: "pointer"
}

function OptionsPage() {
  const [session, setSession] = useState<SessionState | null>(null)
  const [draft, setDraft] = useState<ExtensionSettings | null>(null)
  const [busy, setBusy] = useState<string | null>(null)
  const [status, setStatus] = useState("Loading...")

  useEffect(() => {
    void (async () => {
      const nextSession = await sendBackgroundMessage<SessionState>({
        type: "session:get-state"
      })
      setSession(nextSession)
      setDraft(nextSession.settings)
      setStatus("Settings loaded.")
    })().catch((error) => {
      setStatus(error instanceof Error ? error.message : "Unable to load settings.")
    })
  }, [])

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

  const handleSave = () =>
    runAction("save", async () => {
      if (!draft) {
        throw new Error("No settings to save.")
      }

      const nextSettings = await sendBackgroundMessage<ExtensionSettings>({
        type: "settings:update",
        settings: draft
      })
      setDraft(nextSettings)
      const nextSession = await sendBackgroundMessage<SessionState>({
        type: "session:get-state"
      })
      setSession(nextSession)
      setStatus("Settings updated.")
    })

  const onNumber =
    (field: "lockTimeoutMinutes" | "sensitiveActionWindowSeconds") =>
    (event: React.ChangeEvent<HTMLInputElement>) => {
      const value = Number(event.target.value)
      setDraft((current) =>
        current
          ? {
              ...current,
              [field]: Number.isFinite(value) ? value : 0
            }
          : current
      )
    }

  return (
    <main style={pageStyle}>
      <div style={wrapperStyle}>
        <section style={sectionStyle}>
          <h1 style={{ margin: 0, fontSize: 26 }}>VaultGuardian Extension Settings</h1>
          <div style={{ fontSize: 13, color: colors.textMuted }}>
            Session: {session?.isLocked ? "Locked" : session?.isAuthenticated ? "Connected" : "Signed out"}
          </div>
        </section>

        <section style={sectionStyle}>
          <label style={{ display: "grid", gap: 6 }}>
            <span style={{ fontSize: 13, fontWeight: 600, color: colors.textMuted }}>Backend Base URL</span>
            <input
              type="url"
              value={draft?.backendBaseUrl ?? ""}
              onChange={(event) =>
                setDraft((current) =>
                  current
                    ? {
                        ...current,
                        backendBaseUrl: event.target.value
                      }
                    : current
                )
              }
              style={inputStyle}
            />
          </label>

          <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10 }}>
            <label style={{ display: "grid", gap: 6 }}>
              <span style={{ fontSize: 13, fontWeight: 600, color: colors.textMuted }}>
                Auto-lock (minutes)
              </span>
              <input
                type="number"
                min={1}
                max={120}
                value={draft?.lockTimeoutMinutes ?? 10}
                onChange={onNumber("lockTimeoutMinutes")}
                style={inputStyle}
              />
            </label>

            <label style={{ display: "grid", gap: 6 }}>
              <span style={{ fontSize: 13, fontWeight: 600, color: colors.textMuted }}>
                Sensitive action window (seconds)
              </span>
              <input
                type="number"
                min={15}
                max={600}
                value={draft?.sensitiveActionWindowSeconds ?? 60}
                onChange={onNumber("sensitiveActionWindowSeconds")}
                style={inputStyle}
              />
            </label>
          </div>

          <div style={{ display: "flex", gap: 8 }}>
            <button
              type="button"
              disabled={busy === "save" || !draft}
              onClick={() => void handleSave()}
              style={{
                ...buttonStyle,
                background: colors.primary,
                color: colors.onPrimary
              }}>
              Save
            </button>
            <button
              type="button"
              disabled={busy === "save"}
              onClick={() => window.close()}
              style={{
                ...buttonStyle,
                background: colors.surfaceMuted,
                color: colors.text
              }}>
              Close
            </button>
          </div>
        </section>

        <div style={{ minHeight: 20, fontSize: 13, color: status === "Loading..." ? colors.textMuted : colors.text }}>
          {status}
        </div>
      </div>
    </main>
  )
}

export default OptionsPage

