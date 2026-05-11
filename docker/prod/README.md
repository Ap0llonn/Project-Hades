# Production Docker setup

## 1) Prepare environment

```bash
cp .env.example .env
```

Then update at minimum:

- `APP_KEY` (real generated key)
- `APP_URL`
- `DB_PASSWORD`
- `POSTGRES_PASSWORD`

You can generate a key with:

```bash
docker compose -f docker/prod/compose.yml run --rm app php artisan key:generate --show
```

Then paste that value into `docker/prod/.env` as `APP_KEY=...`.

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
