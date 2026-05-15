import { PlusIcon, ShieldIcon } from "~popup/ui/icons"
import { brandStyle, headerStyle, primaryButtonStyle } from "~popup/ui/styles"

type HeaderProps = {
  isAuthenticated: boolean
  busy: "auth" | "extract" | "save" | null
  canSave: boolean
  displayName: string
  initials: string
  onSave: () => void
}

export const Header = ({
  isAuthenticated,
  busy,
  canSave,
  displayName,
  initials,
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
          title={displayName}
          style={{
            width: 34,
            height: 34,
            borderRadius: 17,
            background: "#6d28d9",
            color: "#fff",
            fontWeight: 700,
            fontSize: 12,
            display: "flex",
            alignItems: "center",
            justifyContent: "center"
          }}>
          {initials}
        </div>
      </div>
    ) : null}
  </header>
)
