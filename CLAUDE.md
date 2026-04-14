# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

A Laravel 11 developer portfolio for a PHP/Laravel backend engineer. The stack is:
- **Laravel 11** + **Livewire 3** (full-page components with `wire:navigate`) for public pages
- **Filament v3** at `/admin` for content management
- **Alpine.js** (bundled with Livewire) for theme toggle and micro-interactions
- **Tailwind 3** via Vite (port 5173 in Docker)
- **Pest 2** for testing
- **spatie/laravel-settings** for site/home/about settings
- MySQL 8.3, Redis (sessions/cache/queue), Mailpit (local mail)

The full design spec is at `docs/superpowers/specs/2026-04-13-portfolio-design.md` and the implementation plan at `docs/superpowers/plans/2026-04-13-portfolio-v1.md`. Read these before touching anything non-trivial.

## Development Environment

All development runs inside Docker via `make`. Do not run PHP/Artisan commands directly on the host.

```bash
make build          # first-time: build images + start containers
make up             # start containers
make down           # stop containers
make ssh            # shell into web container as `application` user
make ssh-root       # shell into web container as root
make fresh          # migrate:fresh --seed
make art            # run artisan as www-data (no args — see art1 for args)
make art1 <cmd>     # run artisan with args, e.g.: make art1 make:model Foo
make fix-perms      # fix storage/cache permissions
make logs           # tail all container logs
make status         # show container health
```

Inside the container, commands run from `/app`:
```bash
php artisan <cmd>
./vendor/bin/pest                    # run all tests
./vendor/bin/pest tests/Feature/     # run feature tests only
./vendor/bin/pest --filter ContactFormTest   # run a single test class
./vendor/bin/pint                    # lint/format PHP (Laravel Pint)
npm run dev                          # Vite dev server (runs on port 5173)
npm run build                        # production asset build
```

**Composer dev script** (runs Laravel + queue + pail + Vite concurrently — host only if PHP installed locally):
```bash
composer dev
```

## Architecture

### Public Side (Livewire)

All public pages are Livewire full-page components in `app/Livewire/Pages/`. Each pairs with a Blade view under `resources/views/livewire/pages/`. Partials live in `app/Livewire/Partials/` with views in `resources/views/livewire/partials/`.

Routes are defined in `routes/web.php` pointing to Livewire classes directly — no controllers needed for public pages.

The shared layout is `resources/views/layouts/app.blade.php` (nav + footer + content slot + dark-mode class management).

### Admin Side (Filament)

Filament resources live in `app/Filament/Resources/`. Settings is a custom Filament page at `app/Filament/Pages/Settings.php` backed by Spatie Settings classes in `app/Settings/`. Dashboard widgets are in `app/Filament/Widgets/`.

The admin panel provider is `app/Providers/Filament/AdminPanelProvider.php`.

### Data

Eloquent models in `app/Models/`. `GithubActivityCache` is a single-row model (updated in place, never inserted twice). Settings are not stored in models — use the Spatie Settings classes.

Rich text fields (`problem`, `approach`, `challenges`, `outcome` on Project; `description` on Experience; `bio` on AboutSettings) are HTML produced by Filament's TipTap editor. Render these with `{!! $model->field !!}` and wrap them in a `prose` Tailwind Typography class. Do not use `{!! !!}` on any other fields.

### Background Jobs

`app/Jobs/FetchGithubActivity.php` runs hourly via the scheduler. On failure it logs and leaves stale cache data in place rather than clearing it. The queue worker runs as its own Docker service.

## Key Conventions

- **Slugs on projects** — stable URLs even if the title changes. Auto-generate on create, allow manual override.
- **`sort_order` everywhere** — all list models have `sort_order` for Filament drag-to-reorder.
- **`is_published` on projects and services** — drafts are never shown on public pages.
- **Dark mode** — Tailwind `darkMode: 'class'`, toggled by Alpine (`ThemeToggle` partial), persisted to `localStorage`, falls back to `prefers-color-scheme` on first visit. No server round-trip.
- **No parallax, no scroll-jacking, no animation libraries.** Scroll-reveal uses a tiny Alpine `IntersectionObserver` directive; hero headline uses CSS keyframes.
- **Fonts via Bunny Fonts** (not Google): `Fraunces` (display), `Inter` (body), `JetBrains Mono` (code/tags).
- **File uploads**: images (cover, architecture, profile, logo) — `jpg|png|webp`, 5 MB max; CV PDF — `pdf`, 10 MB max. Stored under `storage/app/public`, symlinked to `public/storage`.
- **Contact form security**: honeypot field + per-IP rate limit (1/min via `RateLimiter`). Livewire handles CSRF automatically.

## Testing

Tests use Pest 2. The test suite mirrors the `tests/Feature/` and `tests/Unit/` structure:
- `tests/Feature/Pages/` — one file per public page; each page must render 200 with seeded content
- `tests/Feature/Admin/` — access control and resource index tests
- `tests/Unit/Jobs/FetchGithubActivityTest.php` — job tested with mocked HTTP client

Tests run against a real database (SQLite in-memory is commented out in `phpunit.xml` — check current state before assuming). The `QUEUE_CONNECTION` is `sync` in test env.

Browser/Dusk tests are out of scope for v1.

## .env Notes

After first install, set these (the defaults will be wrong):
```
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

`GITHUB_TOKEN` is required for `FetchGithubActivity` — never commit it.
