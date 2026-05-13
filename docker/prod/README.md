# Production Docker setup

## 1) Prepare environment

Use the root project `.env` (`/Project-Hades/.env`).

You can generate a key with:

```bash
docker compose -f docker/prod/compose.yml run --rm app php artisan key:generate --show
```

Then paste that value in root `.env` as `APP_KEY=...`.

## 2) Build and run

```bash
docker compose -f docker/prod/compose.yml up -d --build
```

## 3) One-time app bootstrap

```bash
docker compose -f docker/prod/compose.yml exec app php artisan migrate --force
docker compose -f docker/prod/compose.yml exec app php artisan config:cache
docker compose -f docker/prod/compose.yml exec app php artisan route:cache
docker compose -f docker/prod/compose.yml exec app php artisan view:cache
```

## 4) Optional checks

```bash
docker compose -f docker/prod/compose.yml ps
docker compose -f docker/prod/compose.yml logs -f nginx app worker
```
