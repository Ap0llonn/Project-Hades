import type React from "react"

export const colors = {
  bg: "#f1f5f9",
  surface: "#ffffff",
  surfaceMuted: "#e2e8f0",
  border: "#c6d0e1",
  text: "#111827",
  textMuted: "#475569",
  primary: "#2563eb",
  onPrimary: "#ffffff",
  icon: "#334155"
}

export const shellStyle: React.CSSProperties = {
  width: 420,
  minHeight: 600,
  padding: 0,
  boxSizing: "border-box",
  fontFamily: "'DM Sans', 'Segoe UI', sans-serif",
  background: colors.bg,
  color: colors.text,
  display: "grid",
  gridTemplateRows: "auto 1fr auto"
}

export const headerStyle: React.CSSProperties = {
  height: 66,
  display: "flex",
  alignItems: "center",
  justifyContent: "space-between",
  padding: "0 14px",
  borderBottom: `1px solid ${colors.border}`,
  background: colors.surface
}

export const brandStyle: React.CSSProperties = {
  display: "flex",
  alignItems: "center",
  gap: 8,
  minWidth: 0
}

export const iconButtonStyle: React.CSSProperties = {
  height: 36,
  width: 36,
  borderRadius: 18,
  border: `1px solid ${colors.border}`,
  background: colors.surface,
  color: colors.icon,
  display: "inline-flex",
  alignItems: "center",
  justifyContent: "center",
  cursor: "pointer"
}

export const primaryButtonStyle: React.CSSProperties = {
  border: 0,
  borderRadius: 12,
  height: 40,
  padding: "0 14px",
  fontSize: 14,
  fontWeight: 700,
  cursor: "pointer",
  display: "inline-flex",
  alignItems: "center",
  gap: 8,
  background: colors.primary,
  color: colors.onPrimary
}

export const panelStyle: React.CSSProperties = {
  borderRadius: 12,
  background: colors.surface,
  border: `1px solid ${colors.border}`,
  overflow: "hidden"
}

export const sectionStyle: React.CSSProperties = {
  display: "grid",
  gap: 12
}

export const labelStyle: React.CSSProperties = {
  fontSize: 12,
  fontWeight: 600,
  color: colors.textMuted
}

export const inputStyle: React.CSSProperties = {
  width: "100%",
  borderRadius: 8,
  border: `1px solid ${colors.border}`,
  background: colors.surface,
  color: colors.text,
  padding: "9px 11px",
  fontSize: 14,
  boxSizing: "border-box"
}

export const buttonBase: React.CSSProperties = {
  borderRadius: 10,
  padding: "9px 12px",
  fontSize: 14,
  fontWeight: 700,
  cursor: "pointer"
}
