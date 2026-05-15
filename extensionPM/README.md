# VaultGuardian Extension

Simplified browser extension focused on one workflow:

- authenticate
- capture login fields from the active page
- save a service to the backend

## Run

```bash
npm install
npm run dev
```

Load `build/chrome-mv3-dev` in `chrome://extensions`.

For production:

```bash
npm run build
```

Load `build/chrome-mv3-prod`.

## Expected backend routes

- `GET /login` (web auth flow entry)
- `POST /api/extension/auth/token`
- `POST /api/extension/auth/refresh`
- `POST /api/extension/auth/revoke`
- `POST /api/extension/services` (save service payload)

## Main files

- `popup.tsx`: session actions + save-service form
- `content.ts`: extract username/password/url from active page
- `background.ts`: runtime message router
- `options.tsx`: backend + security settings
- `lib/auth.ts`: token/session management
- `lib/api.ts`: authenticated save-service request
