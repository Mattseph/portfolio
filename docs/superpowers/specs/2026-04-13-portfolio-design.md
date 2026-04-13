# Developer Portfolio — Design Spec

**Date:** 2026-04-13
**Status:** Approved (ready for implementation planning)
**Owner:** Site owner / single admin

## 1. Purpose & Success Criteria

A developer portfolio for a Laravel/PHP backend engineer, used to win jobs and occasional freelance engagements.

**Success criteria**
- Visitors can understand who I am, what I build, and how to reach me within 30 seconds on the homepage.
- Each project has a full case-study page that a hiring manager can skim for interview signal.
- I can add a new project, update bio, or reply to a message without touching code.
- First meaningful paint under 2s on a local Docker run; no blocking external calls on page render.

## 2. Audience & Tone

- **Audience:** engineering hiring managers, recruiters, occasional freelance leads.
- **Identity:** Laravel/PHP backend developer — "engineer who ships," not a designer.
- **Tone:** confident, quiet, evidence-driven. No gradients, no buzzwords, no stock illustrations.

## 3. Visual Direction

**Editorial / Minimal.** Serif display type, sans body, monochrome with a single accent color (color chosen after build; stub with `neutral-900`).

**Typography** (Bunny Fonts — privacy-friendly, no Google calls)
- Display serif: `Fraunces`
- Body sans: `Inter`
- Mono: `JetBrains Mono` (tags, code, technical labels)

**Theme**
- Light + dark toggle.
- Dark mode: deep charcoal background, off-white foreground.
- Tailwind `darkMode: 'class'`, toggled by Alpine, persisted in `localStorage`, falls back to `prefers-color-scheme` on first load.

## 4. Interactivity Level

**Subtle + moderate.** Calm overall with a few signature moments.

- Livewire `wire:navigate` for SPA-feel internal navigation.
- Scroll-reveal via `IntersectionObserver` (tiny Alpine directive).
- Hero headline: word-by-word fade-in on load (CSS keyframes with stagger).
- Skills marquee: CSS `@keyframes` infinite scroll, pauses on hover.
- Project card hover: `hover:-translate-y-1 hover:shadow-lg transition`.
- Theme toggle: smooth color transitions.
- No scroll-jacking, parallax, or animation libraries.

## 5. Site Map & Routes

### Public routes

| URL | Livewire component | Purpose |
|---|---|---|
| `/` | `Pages\Home` | Hero · about teaser · featured projects · skills marquee · GitHub strip · services preview · CTA |
| `/about` | `Pages\About` | Full bio · profile photo · experience timeline · CV PDF download |
| `/projects` | `Pages\ProjectIndex` | All published projects grid; reactive filter by tech-stack tag |
| `/projects/{slug}` | `Pages\ProjectShow` | Full case-study page |
| `/services` | `Pages\Services` | Services list + contact CTA |
| `/contact` | `Pages\Contact` | Contact form (Livewire, validated, honeypot, rate-limited, stores + emails) |

**GitHub feed** is embedded on the homepage as a compact "Recent activity" strip. No dedicated `/activity` page in v1.

### Admin routes (Filament)

- `/admin/login`
- `/admin` — dashboard with *Unread messages* and *Site health* widgets
- `/admin/projects`, `/admin/skills`, `/admin/experiences`, `/admin/services`, `/admin/contact-messages`
- `/admin/settings` — single Filament page with Site / Home / About tabs, backed by `spatie/laravel-settings`

### Navigation

- Top nav: `Home · About · Projects · Services · Contact · [theme toggle] · [GitHub link]`
- Footer: copyright · social links · "built with Laravel" (editable via Settings)

## 6. Case-Study Page Structure

Each project case study includes:

1. **Hero** — title, one-line summary, role, timeframe, tech-stack tags, cover image
2. **Problem / Context** — what this solved, why it mattered
3. **Approach / Architecture** — write-up, optional architecture diagram image
4. **Key challenges** — 2–4 specific hard parts solved
5. **Outcome** — results, numbers where available
6. **Links** — GitHub repo, live demo
7. **Next / Previous project** — navigation at bottom

Intentionally excluded: inline code snippets (GitHub link covers this), image gallery (one cover + one architecture diagram is enough).

## 7. Data Model

```
users
  id, name, email, password, is_admin, timestamps

projects
  id, slug (unique), title, summary,
  role, started_at, ended_at (nullable),
  tech_stack (json),
  cover_image_path, architecture_image_path (nullable),
  problem, approach, challenges, outcome (all rich text HTML),
  repo_url, demo_url (nullable),
  is_featured (bool), sort_order (int),
  is_published (bool), timestamps

skills
  id, name, category (enum: language|framework|database|tool|platform),
  icon (devicon class string, e.g. "devicon-laravel-plain"),
  proficiency (1-5), sort_order, timestamps

experiences
  id, company, role, location,
  started_at, ended_at (nullable — null = "present"),
  description (rich text), logo_path (nullable),
  sort_order, timestamps

services
  id, title, description, icon,
  starting_price (nullable string),
  sort_order, is_published, timestamps

contact_messages
  id, name, email, subject, message,
  ip_address, user_agent,
  is_read (bool), created_at

github_activity_cache
  id, payload (json), fetched_at, timestamps
  (single-row snapshot, updated in place)
```

**Settings via `spatie/laravel-settings`**

```
site_settings:   site_title, tagline, meta_description,
                 github_url, linkedin_url, twitter_url, email,
                 footer_text, is_available_for_work (bool)
home_settings:   hero_headline, hero_subheadline,
                 hero_cta_text, hero_cta_url
about_settings:  bio (rich text), profile_image_path, cv_pdf_path,
                 location, years_experience
```

**Choices**
- Slugs on projects — stable URLs even if title changes.
- Rich text stored as HTML produced by Filament's TipTap editor (constrained subset); rendered with `{!! !!}` on those fields only.
- `is_published` on projects and services enables draft-then-publish workflow.
- `sort_order` everywhere for Filament drag-to-reorder.
- GitHub cache as a single-row JSON snapshot — display-only; separate repos/commits tables add no value.

## 8. Filament Resources (Admin)

| Resource | Notes |
|---|---|
| `ProjectResource` | Tabbed form: *Basic* · *Content* (TipTap) · *Media* (cover + architecture uploads) · *Links*. Drag-reorder list, featured badge, inline publish toggle. |
| `SkillResource` | Simple form; list grouped by category; drag-reorder. |
| `ExperienceResource` | Date pickers; "currently here" checkbox nulls `ended_at`. |
| `ServiceResource` | Simple form; publish toggle. |
| `ContactMessageResource` | Read-only list with unread badge; filter by read/unread; view action shows full message with mark-read + mailto reply. |
| `SettingsPage` | Custom Filament page with Site / Home / About tabs backed by Spatie Settings. |

**Dashboard widgets**
- *Unread messages* — count + link.
- *Site health* — "Available for work: yes/no", "Last GitHub sync: Nh ago".

## 9. Livewire Components (Public)

| Component | Type | Behavior |
|---|---|---|
| `Pages\Home` | Full-page | Hero · featured projects · skills marquee · GitHub strip · services preview · CTA |
| `Pages\About` | Full-page | Bio · experience timeline · CV download |
| `Pages\ProjectIndex` | Full-page | Projects grid with reactive tech-stack filter (no reload) |
| `Pages\ProjectShow` | Full-page | Single case-study render |
| `Pages\Services` | Full-page | Services list |
| `Pages\Contact` | Full-page | Form with real-time validation, honeypot, rate limit, stores + queues email |
| `Partials\SkillsMarquee` | Partial | CSS-only infinite marquee of skill icons |
| `Partials\GithubStrip` | Partial | Reads `github_activity_cache`, renders compact feed; hides if empty |
| `Partials\ThemeToggle` | Alpine only | Toggles `dark` class on `<html>`, persists to `localStorage`; no server round-trip |

## 10. Background Jobs & Scheduling

- `FetchGithubActivity` — queued job, scheduled **hourly** via `app/Console/Kernel.php`. Uses `GITHUB_TOKEN` from `.env`. Writes to `github_activity_cache`. On failure: logs, leaves stale data in place.
- `SendContactNotificationMail` — queued on contact form submit. Uses Mailpit locally.

Queue worker already runs in the existing `docker-compose.yml`.

## 11. Styling & Theming

- **Tailwind 3** via Vite (port 5173 already exposed in Docker).
- `tailwind.config.js`: `darkMode: 'class'`; custom fonts; neutral palette; `@tailwindcss/typography` plugin for rendering rich-text case-study bodies.
- One shared Blade layout: `resources/views/layouts/app.blade.php` with nav + footer + content slot.
- **Filament panel** uses its default theme with only the primary color matched to the site accent. No deeper rebranding of the admin UI.

## 12. Error Handling & Validation

**Contact form**
- Rules: `name` required max 100 · `email` required valid · `subject` required max 150 · `message` required max 5000 · honeypot must be empty.
- Rate limit: 1 submission / minute / IP via Laravel's `RateLimiter`.
- Success: flash message + form reset. Failure: inline field errors.

**Filament**
- Built-in validation on resources; unique slug auto-enforced.

**GitHub fetch**
- Job catches API failures; logs; does not overwrite cache on failure (stale > empty).
- Homepage strip hides gracefully when cache is empty (first run).

**Error pages**
- Custom `resources/views/errors/404.blade.php` matching site aesthetic.
- Generic `500.blade.php`; full trace only when `APP_DEBUG=true`.

## 13. Security

- Single seeded admin; no registration route exposed; Filament login only.
- GitHub token in `.env` only; never committed.
- Rich text produced only via Filament's TipTap editor, which emits a constrained HTML subset; rendered with `{!! !!}` on those fields only. No third-party sanitizer needed.
- File uploads validated by Filament with explicit limits: images (cover, architecture, profile, experience logo) — `jpg|png|webp`, 5 MB max; CV PDF — `pdf`, 10 MB max. Stored under `storage/app/public`; symlinked to `public/storage`.
- CSRF handled by Livewire automatically.
- Contact form: honeypot + per-IP rate limit.

## 14. Testing

**Framework:** Pest (Laravel 11 default).

**Feature tests**
- Each public page renders 200 with seeded content.
- Contact form: valid submission stores + queues mail · invalid shows errors · honeypot rejects · rate limit triggers on second submission within a minute.
- Admin: unauthenticated redirects · non-admin gets 403 · admin gets 200 on each resource index.

**Unit tests**
- `FetchGithubActivity` job with mocked HTTP client: success path · API failure keeps stale data · first-run with empty cache.

**Out of scope for v1**
- Browser/Dusk tests — not worth the cost for a portfolio; rely on manual browser check.

## 15. Deployment & Infrastructure

**v1 runs locally only** via the existing `docker-compose.yml` (web, queue, mysql, redis, phpmyadmin, mailpit). Production deployment is explicitly deferred.

Existing compose services are kept as-is; Laravel install targets the existing container layout (doc root `/app/public`, MySQL host `mysql`, Redis host `redis`, Mailpit at `mailpit:1025`).

Swap defaults once Laravel is installed:
- `SESSION_DRIVER=redis`
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`

## 16. Directory Layout

```
app/
  Filament/
    Resources/{Project,Skill,Experience,Service,ContactMessage}Resource.php
    Pages/Settings.php
    Widgets/{UnreadMessages,SiteHealth}Widget.php
  Livewire/
    Pages/{Home,About,ProjectIndex,ProjectShow,Services,Contact}.php
    Partials/{SkillsMarquee,GithubStrip}.php
  Jobs/FetchGithubActivity.php
  Mail/ContactNotification.php
  Models/{User,Project,Skill,Experience,Service,ContactMessage,GithubActivityCache}.php
  Settings/{SiteSettings,HomeSettings,AboutSettings}.php
resources/
  views/
    layouts/app.blade.php
    livewire/pages/*.blade.php
    livewire/partials/*.blade.php
    errors/{404,500}.blade.php
  css/app.css
database/
  migrations/*
  seeders/DatabaseSeeder.php           # seeds admin user + demo content
docs/superpowers/specs/2026-04-13-portfolio-design.md
```

## 17. Deferred / Out of Scope

- Blog / articles
- Testimonials
- Multi-user admin with roles
- i18n (single language for v1)
- Analytics (can add Plausible/Umami via Settings later)
- `sitemap.xml` generation (small v1.1 add)
- Accent color pick (stub `neutral-900` until the real site is visible)
- Production deployment (Forge / Cloud / VPS — decided post-v1)
- Inline code snippets and multi-image galleries on case studies
