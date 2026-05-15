import { colors } from "~popup/ui/styles"

type StatusFooterProps = {
  status: string
  isAuthenticated: boolean
  email?: string
}

export const StatusFooter = ({ status, isAuthenticated, email }: StatusFooterProps) => (
  <footer
    style={{
      minHeight: 24,
      borderTop: `1px solid ${colors.border}`,
      fontSize: 12,
      color: status ? colors.text : colors.textMuted,
      padding: "5px 12px",
      background: colors.surface
    }}>
    {status || (isAuthenticated ? `Logged in as ${email ?? "user"}` : "Not authenticated")}
  </footer>
)
