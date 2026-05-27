# Matthew Bilaos — Developer Portfolio

Personal portfolio site for Matthew Joseph F. Bilaos, PHP/Laravel Developer. Built with Laravel 11, Livewire 3, and Filament v3.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Livewire 3, Alpine.js, Tailwind CSS 3 |
| Admin | Filament v3 (`/admin`) |
| Database | MySQL 8.3 |
| Cache / Queue | Redis |
| Mail (local) | Mailpit |
| Containerization | Docker |
| Testing | Pest 2 |

## Features

- **Public pages** — Home, About, Projects (with tech filter), Services, Contact
- **Filament admin** — Manage projects, skills, experiences, services, contact messages, and all site settings
- **GitHub activity** — Hourly job syncs latest activity via `FetchGithubActivity`
- **Dark mode** — Tailwind `class` strategy, toggled via Alpine.js, persisted to `localStorage`
- **Contact form** — Livewire with honeypot and per-IP rate limiting (1/min), queued mail dispatch
- **Settings** — Spatie Laravel Settings for site, home, and about content (no hardcoded copy)

## Development Setup

All commands run inside Docker. Do not run PHP/Artisan directly on the host.

```bash
make build       # first-time: build images and start containers
make up          # start containers
make down        # stop containers
make ssh         # shell into web container
make fresh       # migrate:fresh --seed (resets all data)
make logs        # tail container logs
make status      # show container health
```

Inside the container (`make ssh`):

```bash
php artisan <cmd>
./vendor/bin/pest                          # run all tests
./vendor/bin/pest --filter ProjectTest     # run a single test class
./vendor/bin/pint                          # lint PHP
npm run dev                                # Vite dev server (port 5173)
npm run build                              # production assets
```

## Environment

Copy `.env.example` to `.env` and set:

```env
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

GITHUB_TOKEN=your_token_here   # required for GitHub activity sync
```

## Seeding

```bash
make fresh
```

Seeds real portfolio data including skills, experiences, services, and projects. Admin login after seeding:

- **Email:** `matthewjoseph.bilaos@gmail.com`
- **Password:** `password` — change this after first login

## Testing

```bash
make ssh
./vendor/bin/pest
```

Tests live in `tests/Feature/` (pages, admin) and `tests/Unit/` (jobs). The `QUEUE_CONNECTION` is `sync` in the test environment.

## Project Structure

```
app/
  Livewire/Pages/      # Full-page Livewire components (public)
  Livewire/Partials/   # Shared partials (nav, footer, theme toggle)
  Filament/            # Admin resources, pages, widgets
  Models/              # Eloquent models
  Settings/            # Spatie Settings classes (SiteSettings, HomeSettings, AboutSettings)
  Jobs/                # FetchGithubActivity
resources/
  views/livewire/      # Blade views for Livewire components
  views/layouts/       # Shared app layout
  css/app.css          # Tailwind entry point
database/
  seeders/             # PortfolioDataSeeder (real data)
  settings/            # Spatie settings migrations
```
