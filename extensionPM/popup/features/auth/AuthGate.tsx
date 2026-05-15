import { buttonBase, colors, panelStyle } from "~popup/ui/styles"

type AuthGateProps = {
  busy: "auth" | "extract" | "save" | null
  email: string
  password: string
  onEmailChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onLogin: () => void
  onCreateAccount: () => void
}

export const AuthGate = ({
  busy,
  email,
  password,
  onEmailChange,
  onPasswordChange,
  onLogin,
  onCreateAccount
}: AuthGateProps) => (
  <section style={{ padding: 14, display: "grid", alignContent: "center" }}>
    <form
      onSubmit={(event) => {
        event.preventDefault()
        onLogin()
      }}
      style={{ ...panelStyle, padding: 16, display: "grid", gap: 14 }}>
      <h2 style={{ margin: 0, fontSize: 19 }}>Login to Vault</h2>
      <label style={{ display: "grid", gap: 6 }}>
        <span style={{ fontSize: 12, fontWeight: 600, color: colors.textMuted }}>Email</span>
        <input
          type="email"
          autoComplete="username"
          value={email}
          onChange={(event) => onEmailChange(event.target.value)}
          style={{
            width: "100%",
            borderRadius: 8,
            border: `1px solid ${colors.border}`,
            background: colors.surface,
            color: colors.text,
            padding: "9px 11px",
            fontSize: 14,
            boxSizing: "border-box"
          }}
        />
      </label>
      <label style={{ display: "grid", gap: 6 }}>
        <span style={{ fontSize: 12, fontWeight: 600, color: colors.textMuted }}>Password</span>
        <input
          type="password"
          autoComplete="current-password"
          value={password}
          onChange={(event) => onPasswordChange(event.target.value)}
          style={{
            width: "100%",
            borderRadius: 8,
            border: `1px solid ${colors.border}`,
            background: colors.surface,
            color: colors.text,
            padding: "9px 11px",
            fontSize: 14,
            boxSizing: "border-box"
          }}
        />
      </label>
      <button
        type="submit"
        disabled={busy === "auth"}
        style={{
          ...buttonBase,
          border: 0,
          background: colors.primary,
          color: colors.onPrimary
        }}>
        {busy === "auth" ? "Signing in..." : "Login"}
      </button>
      <button
        type="button"
        onClick={onCreateAccount}
        style={{
          ...buttonBase,
          border: `1px solid ${colors.border}`,
          background: colors.surface,
          color: colors.text
        }}>
        Create account
      </button>
    </form>
  </section>
)
