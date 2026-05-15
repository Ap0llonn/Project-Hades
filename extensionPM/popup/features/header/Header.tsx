import { PlusIcon, ShieldIcon } from "~popup/ui/icons"
import { brandStyle, headerStyle, primaryButtonStyle } from "~popup/ui/styles"

type HeaderProps = {
  isAuthenticated: boolean
  busy: "auth" | "extract" | "save" | null
  canSave: boolean
  userEmail: string
  emailInitial: string
  onSave: () => void
}

export const Header = ({
  isAuthenticated,
  busy,
  canSave,
  userEmail,
  emailInitial,
  onSave
}: HeaderProps) => (
  <header style={headerStyle}>
    <div style={brandStyle}>
      <ShieldIcon />
      <h1 style={{ margin: 0, fontSize: 30, lineHeight: 1, fontWeight: 800 }}>Vault</h1>
    </div>
    {isAuthenticated ? (
      <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
        <button
          type="button"
          disabled={busy === "save" || !canSave}
          onClick={onSave}
          style={{
            ...primaryButtonStyle,
            opacity: busy === "save" || !canSave ? 0.65 : 1
          }}>
          <PlusIcon />
          Save
        </button>
        <div
          title={userEmail}
          style={{
            display: "flex",
            alignItems: "center",
            gap: 8,
            border: "1px solid #c6d0e1",
            borderRadius: 17,
            padding: "4px 10px 4px 4px",
            background: "#ffffff",
            maxWidth: 180
          }}>
          <span
            style={{
              width: 26,
              height: 26,
              borderRadius: 13,
              background: "#6d28d9",
              color: "#fff",
              fontWeight: 700,
              fontSize: 12,
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              flex: "0 0 auto"
            }}>
            {emailInitial}
          </span>
          <span
            style={{
              fontSize: 12,
              fontWeight: 600,
              color: "#334155",
              whiteSpace: "nowrap",
              overflow: "hidden",
              textOverflow: "ellipsis"
            }}>
            {userEmail}
          </span>
        </div>
      </div>
    ) : null}
  </header>
)
