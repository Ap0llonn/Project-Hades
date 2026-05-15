import { AuthGate } from "~popup/features/auth/AuthGate"
import { Dashboard } from "~popup/features/dashboard/Dashboard"
import { StatusFooter } from "~popup/features/footer/StatusFooter"
import { Header } from "~popup/features/header/Header"
import { usePopupController } from "~popup/model/usePopupController"
import { shellStyle } from "~popup/ui/styles"

function IndexPopup() {
  const controller = usePopupController()
  const { actions, busy, draft, search, session, status, tab, view } = controller

  return (
    <main style={shellStyle}>
      <Header
        isAuthenticated={view.isAuthenticated}
        busy={busy}
        canSave={view.canSave}
        displayName={view.displayName}
        initials={view.initials}
        onSave={() => void actions.saveService()}
      />

      {!view.isAuthenticated ? (
        <AuthGate
          busy={busy}
          email={controller.loginDraft.email}
          password={controller.loginDraft.password}
          onEmailChange={controller.setLoginEmail}
          onPasswordChange={controller.setLoginPassword}
          onLogin={() => void actions.authenticate()}
          onCreateAccount={actions.createAccount}
        />
      ) : (
        <Dashboard
          busy={busy}
          search={search}
          draft={draft}
          tab={tab}
          matchesSearch={view.matchesSearch}
          onSearchChange={controller.setSearch}
          onInput={controller.onInput}
          onExtract={() => void actions.extractFromPage()}
        />
      )}

      <StatusFooter
        status={status}
        isAuthenticated={view.isAuthenticated}
        email={session?.user?.email}
      />
    </main>
  )
}

export default IndexPopup
