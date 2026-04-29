# Project Status - 2026-04-29

## Executive Summary

Yalumni is currently a Laravel 9.52.16 application with a Blade/Laravel Mix frontend, public alumni portal pages, authenticated alumni workflows, admin workflows, super-admin workflows, API/payment callback routes, optional addon hooks, and multi-tenancy support through `stancl/tenancy`.

The current branch is `devLocalBuid`, tracking `origin/devLocalBuid`. The branch contains the repository bootstrap and addon boot hardening work that allows the app to register routes even when optional addon payloads are not installed into the executable app tree.

The app currently boots successfully:

- `php artisan route:list` succeeds and reports 360 routes.
- `php artisan test` succeeds with 8 passing tests.
- `composer validate --no-check-publish` succeeds with one warning.

The largest open risks are dependency/security hygiene and reproducibility:

- `composer.lock` is missing.
- `package-lock.json` is missing.
- `composer audit` reports security advisories in installed PHP dependencies.
- `npm audit` cannot run because there is no npm lockfile.
- JavaScript dependencies are old and the build cannot be reproduced from a committed lockfile.

## Current Project Shape

### Stack

- Backend: Laravel 9.52.16 on PHP 8.2.12.
- Frontend: Blade templates, Laravel Mix 6, Bootstrap 5, Sass.
- Auth: Laravel UI auth, email verification helpers, social login controllers, Google 2FA support.
- Tenancy: `stancl/tenancy`, with central-domain aware route registration.
- Payments: multiple gateway service classes, including Stripe, PayPal, Razorpay, Paytm, Mollie, Mercado Pago, Iyzipay, Authorize.Net, BitPay, Twilio-related SMS flows, and others.
- Admin: role/permission based admin area using Spatie Permission.
- Installer/update flow: Zainik installer and app/addon version update controllers.

### Codebase Inventory

- 81 controllers under `app/Http/Controllers`.
- 62 service classes under `app/Http/Services`.
- 67 Eloquent models under `app/Models`.
- 95 database migrations.
- 251 Blade templates.
- 878 files under `public`.
- 360 registered Laravel routes in the current local boot mode.

### Route Areas

- Public frontend: `/`, `/all-alumni`, `/all-event`, `/our-news`, `/our-notice`, `/all-membership`, `/all-job`, `/all-stories`, `/contact-us`, and dynamic content pages.
- Auth: login/register/password reset, Google/Facebook login, Google 2FA verification.
- Alumni/user app: `/home`, profile/settings, events, jobs, stories, posts, notices/news, memberships, checkout, transactions, chat.
- Admin: `/admin/dashboard`, content management, alumni moderation, roles, settings, website settings, gateways, newsletters, transactions, version/addon updates.
- Super admin: `/super-admin/dashboard`, profile, settings, language, maintenance, storage, version/addon updates.
- API: `/api/verify` payment callback plus default user API route.
- Optional addons: SaaS, donation, and committee route groups are guarded by addon bootstrap detection.

## Completed / Current Implementations

### Repository Bootstrap

- Git repository is initialized and connected to `https://github.com/Tyrax39/Yalumni_V0.git`.
- `.gitignore` excludes local env files, dependencies, caches, logs, storage runtime files, addon payload artifacts, and the local archive.
- PR template exists at `.github/pull_request_template.md`.
- Bootstrap documentation exists at `docs/repo-bootstrap-and-addon-hardening.md`.

### Addon Boot Hardening

- Addon detection only reports an addon as installed when its required executable bootstrap files exist.
- Optional SaaS, donation, and committee routes are guarded so route registration does not crash when addon artifacts are present only in storage.
- Addon helper functions avoid `optimize:clear` side effects during read-only version checks.
- Tests cover addon bootstrap mapping, central domain helper behavior, route registration without optional addons, and core auth/alumni route registration.

### Core Functional Surface

- Public website pages exist for home, pages, alumni, events, news, notices, membership, jobs, stories, contact, and ticket verification.
- Alumni portal supports profile, settings, feed/posts, chat, event creation/tickets, jobs, stories, memberships, checkout, and transactions.
- Admin supports alumni moderation, role/permission management, content taxonomies, website settings, currencies, gateways, language, newsletters, transactions, and version/addon updates.
- Super-admin supports global configuration, storage/cache/maintenance controls, language, addon/version updates, and SaaS-related settings when that addon is installed.
- Payment service layer is broad, covering many gateways, but needs deeper automated verification because payment callbacks and external SDKs are high-risk.

## Existing Gaps

### Reproducibility

- Missing `composer.lock` means PHP dependency resolution is not reproducible.
- Missing `package-lock.json` means JavaScript dependency resolution is not reproducible and `npm audit` cannot run.
- `vendor/` and `node_modules/` are intentionally ignored, so lockfiles are the correct source of reproducibility.
- No CI workflow is present yet to enforce install, route registration, tests, and dependency audits.

### Dependency And Security Hygiene

- `composer audit` reports advisories in installed dependencies, including high severity issues.
- Laravel is installed at `v9.52.16`; Composer reports `v12.58.0` as latest and `v9.52.17` or later is needed for at least one Laravel 9 security advisory.
- `php-http/message-factory` is abandoned and should be replaced through upstream dependency updates where possible.
- Several payment SDKs are multiple major versions behind, which increases payment integration and security risk.
- `composer validate` warns that `mollie/laravel-mollie` is pinned to exact version `2.19`; if semver-compatible, loosen or intentionally document that pin.
- `npm audit` cannot run until a package lock exists.
- `npm outdated --all` reports `laravel-datatables-vite` wanted `0.5.3`, latest `0.6.2`.

### Test Coverage

- Current tests are useful smoke/route tests, but coverage is still thin for a project of this size.
- No feature tests cover authentication flows, role permissions, admin CRUD, alumni CRUD, posts/comments/likes, chat, checkout, or payment callbacks.
- No browser/end-to-end tests cover the frontend, admin, or alumni portal.
- No migration test proves a clean database can install from scratch.
- No seed/demo-data test proves a local environment can be populated consistently.

### Frontend Build

- `webpack.mix.js` only builds `resources/js/app.js` and `resources/sass/app.scss`.
- Many production assets under `public/` appear prebuilt or vendor-style; it is unclear which are source assets and which are generated assets.
- `node_modules` is absent and no lockfile exists, so frontend build verification requires dependency resolution first.
- There is no documented frontend build pipeline beyond Laravel Mix scripts.

### Operations

- Local `.env` exists, but environment setup is not documented in a committed example file in this checkout.
- The CLI emits repeated `Module "openssl" is already loaded` warnings, indicating a PHP/XAMPP configuration duplication that should be cleaned up locally and in setup docs.
- No queue worker, scheduler, backup, or mail verification runbook is documented.
- Payment gateway, SMS, social login, storage, and tenancy configuration all need environment-specific runbooks.

### Architecture / Maintainability

- The payment service layer is broad and should be normalized around a documented gateway contract plus callback test fixtures.
- Controllers and services are numerous; high-change flows should be given module-level docs before larger refactors.
- Some route names are duplicated or overloaded, for example password update and language translate route names. These should be reviewed before route caching is enabled in production.
- Optional addon routing is now safer, but addon lifecycle tests should be expanded to cover installed-addon mode with actual bootstrap files.

## Dependency Update Needs

### Immediate Security Updates

Prioritize a narrow Composer security update PR before broad major upgrades:

- Update Laravel 9 to at least a patched 9.52.x release or plan the Laravel 10/11/12 upgrade path.
- Update `yansongda/pay` from `v3.7.9` to at least `v3.7.20`.
- Update transitive Symfony components through Laravel-compatible constraints.
- Update `league/commonmark`, `nesbot/carbon`, `phpseclib/phpseclib`, `psy/psysh`, and `phpunit/phpunit` through compatible direct package updates.
- Update AWS SDK through `league/flysystem-aws-s3-v3` compatible constraints.
- Update `laravel/socialite` to pull safer versions of transitive auth/crypto packages where possible.

### Reproducibility Updates

Create lockfiles in their own PRs:

- Generate and commit `composer.lock` from a known-good PHP environment.
- Generate and commit `package-lock.json`.
- After lockfiles exist, run `composer install`, `composer audit`, `npm ci`, `npm audit`, `npm run production`, `php artisan route:list`, and `php artisan test`.

### Major Upgrade Candidates

These need separate planning and regression testing:

- Laravel 9 to Laravel 10/11/12.
- Sanctum 3 to 4.
- Debugbar 3 to 4.
- Doctrine DBAL 3 to 4.
- Stripe PHP 9 to 20.
- Twilio SDK 7 to 8.
- Mollie Laravel 2 to 4.
- Mercado Pago SDK 2 to 3.
- Yajra DataTables 10 to 12.
- Zai Installer 1 to 2.
- PHPUnit 9 to 11 or 12, aligned with the target Laravel version.

## Recommended Next Implementations

### Priority 1 - Stabilize Builds And Security

- Add `composer.lock` and `package-lock.json`.
- Add a GitHub Actions CI workflow for PHP install, route list, tests, Composer validate, Composer audit, npm install/build/audit.
- Patch high-severity Composer audit findings with the smallest compatible update set.
- Document local setup, required PHP extensions, database creation, migration/seed/import steps, and test credentials.

### Priority 2 - Prove Core Workflows

- Add feature tests for login/register/password reset.
- Add admin route permission tests for admin and super-admin boundaries.
- Add CRUD tests for alumni, events, jobs, news, notices, memberships, and website settings.
- Add payment callback contract tests with gateway fixture payloads.
- Add a clean-install migration test against SQLite or an isolated MySQL database.

### Priority 3 - Frontend/Admin Verification

- Make the frontend build reproducible with npm lockfile and `npm ci`.
- Add at least one browser smoke test for:
  - public frontend home page,
  - login page,
  - admin dashboard redirect/auth boundary,
  - super-admin dashboard redirect/auth boundary,
  - alumni home redirect/auth boundary.
- Document which assets are source-controlled static assets and which are generated by Mix.

### Priority 4 - Operational Readiness

- Add deployment checklist for cache, config, routes, storage link, queue worker, scheduler, mail, backup, and payment webhooks.
- Add environment variable reference without secrets.
- Add runbooks for payment gateway onboarding, SMS/mail verification, social login callbacks, S3/storage, tenancy domains, and addon installation.
- Add route-cache/config-cache validation once duplicate route names are reviewed.

## Validation Snapshot

Commands run on 2026-04-29:

- `php artisan route:list` - passed, 360 routes.
- `php artisan test` - passed, 8 tests.
- `composer validate --no-check-publish` - passed with warning about exact `mollie/laravel-mollie` constraint.
- `composer outdated --direct --format=json` - completed and reported multiple direct updates.
- `composer audit --format=json` - failed because advisories are present.
- `npm outdated --all --json` - completed and reported `laravel-datatables-vite` update availability.
- `npm audit --json` - failed because `package-lock.json` is missing.
- `npm run production` - failed because `mix` is unavailable without installed npm dependencies.

## Local Verification URLs

When the Laravel development server is running on port 8000:

- Public frontend: `http://127.0.0.1:8000/`
- Admin area: `http://127.0.0.1:8000/admin/dashboard`
- Super-admin area: `http://127.0.0.1:8000/super-admin/dashboard`
- Backend/API callback route: `http://127.0.0.1:8000/api/verify`

Admin, super-admin, and alumni routes require authentication and the configured local database/user state.

## Recommended PR Sequence

1. Project status and documentation report.
2. Add lockfiles and CI.
3. Patch Composer security advisories.
4. Reproducible frontend build and npm audit cleanup.
5. Core workflow feature tests.
6. Browser smoke tests for public, auth, admin, super-admin, and alumni routes.
7. Payment gateway contract tests and operational runbooks.
