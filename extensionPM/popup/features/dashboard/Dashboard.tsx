import type { ActiveTabInfo, ServiceDraft } from "~lib/types"
import { FilterIcon, SearchIcon } from "~popup/ui/icons"
import {
  iconButtonStyle,
  inputStyle,
  labelStyle,
  panelStyle,
  sectionStyle,
  colors
} from "~popup/ui/styles"

type DashboardProps = {
  busy: "auth" | "extract" | "save" | null
  search: string
  draft: ServiceDraft
  tab: ActiveTabInfo | null
  matchesSearch: boolean
  onSearchChange: (value: string) => void
  onInput: (field: keyof ServiceDraft, value: string) => void
  onExtract: () => void
}

export const Dashboard = ({
  busy,
  search,
  draft,
  tab,
  matchesSearch,
  onSearchChange,
  onInput,
  onExtract
}: DashboardProps) => (
  <section
    style={{
      padding: 12,
      display: "grid",
      alignContent: "start",
      gap: 12,
      overflowY: "auto"
    }}>
    <div style={{ display: "flex", gap: 8 }}>
      <label
        style={{
          ...panelStyle,
          display: "flex",
          alignItems: "center",
          gap: 8,
          flex: 1,
          padding: "0 10px",
          height: 38
        }}>
        <span style={{ color: colors.textMuted, display: "inline-flex" }}>
          <SearchIcon />
        </span>
        <input
          value={search}
          onChange={(event) => onSearchChange(event.target.value)}
          placeholder="Search"
          style={{ border: 0, outline: "none", background: "transparent", width: "100%", fontSize: 14 }}
        />
      </label>
      <button
        type="button"
        title="Read from page"
        disabled={busy === "extract"}
        onClick={onExtract}
        style={iconButtonStyle}>
        <FilterIcon />
      </button>
    </div>

    <div style={sectionStyle}>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
        <h2 style={{ margin: 0, fontSize: 24 }}>Suggested</h2>
        <span style={{ fontSize: 12, color: colors.textMuted }}>{matchesSearch ? 1 : 0}</span>
      </div>
      {matchesSearch ? (
        <div style={{ ...panelStyle, padding: 12, display: "grid", gap: 5 }}>
          <div style={{ fontSize: 15, fontWeight: 700 }}>{draft.name || "Current tab"}</div>
          <div style={{ fontSize: 12, color: colors.textMuted }}>
            {draft.username || tab?.title || "No username extracted yet"}
          </div>
          <div style={{ fontSize: 12, color: colors.textMuted, overflow: "hidden", textOverflow: "ellipsis" }}>
            {tab?.url ?? draft.url}
          </div>
        </div>
      ) : null}
    </div>

    <div style={sectionStyle}>
      <h2 style={{ margin: 0, fontSize: 24 }}>Save item</h2>
      <div style={{ ...panelStyle, padding: 12, display: "grid", gap: 10 }}>
        <label style={{ display: "grid", gap: 6 }}>
          <span style={labelStyle}>Item Name</span>
          <input value={draft.name} onChange={(event) => onInput("name", event.target.value)} style={inputStyle} />
        </label>

        <label style={{ display: "grid", gap: 6 }}>
          <span style={labelStyle}>Website URL</span>
          <input value={draft.url} onChange={(event) => onInput("url", event.target.value)} style={inputStyle} />
        </label>

        <label style={{ display: "grid", gap: 6 }}>
          <span style={labelStyle}>Username / Email</span>
          <input
            value={draft.username}
            onChange={(event) => onInput("username", event.target.value)}
            style={inputStyle}
          />
        </label>

        <label style={{ display: "grid", gap: 6 }}>
          <span style={labelStyle}>Password</span>
          <input
            type="password"
            value={draft.password}
            onChange={(event) => onInput("password", event.target.value)}
            style={inputStyle}
          />
        </label>
      </div>
    </div>
  </section>
)
