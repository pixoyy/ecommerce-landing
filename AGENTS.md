# AGENTS.md — admin-protogym

Laravel 10 (PHP ^8.1) gym management admin panel. Staff model is the primary auth user, not the default Laravel User.

## Commands

```sh
# test (PHPUnit 10)
./vendor/bin/phpunit
php artisan test

# lint (Laravel Pint, default rules, no custom config)
./vendor/bin/pint

# frontend assets (Vite 4, not Laravel Mix)
npm run dev
npm run build

# queue (database driver, table: jobs_admin)
php artisan queue:work --queue=default,hikvision-update

# codegen / docs (dev)
php artisan scribe:generate
```

## Architecture

- **Auth guards**: `web` (session) for admin panel routes, `api` (Passport OAuth2) for API. Sanctum also present.
- **Staff roles** (config/auth.php): Super Admin=1, Admin=2, Sales=3, Personal Trainer=4, Instructor=5.
- **Routes**: Most logic is in `routes/web.php` (admin panel). `routes/api.php` is minimal. GraphQL at `/graphql` (Lighthouse, schema in `graphql/schema.graphql`).
- **Queue**: Default `database` connection (`jobs_admin` table). Redis for `hikvision-update` queue. Failed jobs: `failed_jobs_admin`.
- **Helpers**: `app/Helpers/Helper.php` is auto-loaded via `composer.json` autoload.files.
- **Cache/Session**: `file` driver locally, Redis (`roxit:alpha`/`roxit:live` prefix) on deployed environments.
- **Storage**: S3-compatible (`storage.dextion.com`) via `league/flysystem-aws-s3-v3`.

## Testing notes

- `phpunit.xml` has DB config **commented out** — tests use `array` cache, `sync` queue. If tests need a DB, uncomment the sqlite block.
- Only example tests exist (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`).

## Deployment

- Docker images built per branch (alpha/main/v2) via GitHub Actions.
- Kubernetes deployment: `protogym-s` namespace (staging), `protogym` (production).
- `AUTORUN_LARAVEL_MIGRATION=true` for alpha, `false` for production.
- Multiple Dockerfiles: `Dockerfile.alpha` (serversideup/php:8.2-fpm-nginx), `Dockerfile.main` (production, no dev deps).
- Skaffold for local K8s dev.

## Environment

- Env files: `.env` (local), `.env.alpha` (staging), `.env.main` (production). Copy from `.env.example` for initial setup.
- Timezone: `Asia/Jakarta`, locale: `id`, faker locale: `id_ID`.
- Key integrations: Sentry (`sentry.pmberjaya.com`), Firebase Cloud Messaging, Hikvision cameras, Accurate/JOVA accounting.

## Notable

- 254+ migrations — schema-heavy app.
- No JS linter/formatter (no ESLint, Prettier, TypeScript).
- No pre-commit hooks.
- No Makefile.
- `composer.lock` and `.env` are gitignored.

## Development Workflow
Phase-based development tracked in `docs/planning/`. Each phase is planned, executed, verified, and tested before moving to the next.
### Workflow
```
PHASE PLANNING
  1. Review todo.md / roadmap.md / summary.md
  2. Discuss phase requirements with user
  3. Create phase-XX-name folder
  4. Write plan.md (step-by-step implementation)
  5. Write verification.md (testing checklist)
  6. Write human-uat.md (manual testing scenarios)
IMPLEMENTATION
  7. Execute plan.md steps ONE AT A TIME:
     a. Do one step fully
     b. Mark step [x] complete in plan.md
     c. STOP — wait for user confirmation before next step
  8. Update summary.md with progress
VERIFICATION & TESTING
  9. Run verification.md checklist
  10. Run human-uat.md scenarios
  11. Fix issues found
  12. User approval
PHASE COMPLETE
  13. Update summary.md, todo.md and roadmap.md (phase completed)
  14. Create COMPLETE.md as the phase summary
  15. Move to next phase
```
