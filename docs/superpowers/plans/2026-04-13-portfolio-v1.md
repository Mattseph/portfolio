# Portfolio v1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a Laravel/Livewire/Filament developer portfolio that runs locally in Docker, with a public site (home, about, projects, services, contact) and a Filament admin for content management — matching the approved spec at `docs/superpowers/specs/2026-04-13-portfolio-design.md`.

**Architecture:** Single Laravel 11 app behind the existing webdevops/php-nginx:8.3 container. Livewire full-page components for public routes, Filament v3 for admin at `/admin`, MySQL for data, Redis for sessions/cache/queue after install, Mailpit for local mail. Tailwind 3 via Vite. Content driven by a handful of Eloquent models plus Spatie Settings.

**Tech Stack:** PHP 8.3, Laravel 11, Livewire 3 (`wire:navigate`), Alpine.js (bundled with Livewire), Filament v3, Tailwind 3, Vite 5, Pest 2, `spatie/laravel-settings`, MySQL 8.3, Redis (alpine), Mailpit.

---

## Phase Map

- **Phase 0 — Repo housekeeping** (Tasks 1–2)
- **Phase 1 — Laravel + Docker boot** (Tasks 3–6)
- **Phase 2 — Frontend toolchain & base layout** (Tasks 7–10)
- **Phase 3 — Data model, settings, factories, seeder** (Tasks 11–18)
- **Phase 4 — Filament admin** (Tasks 19–25)
- **Phase 5 — Public pages & partials** (Tasks 26–34)
- **Phase 6 — Background jobs & scheduling** (Tasks 35–36)
- **Phase 7 — Error pages & final polish** (Tasks 37–38)

Each task ends with a commit. Run `./vendor/bin/pest` at any point to see the full test suite.

---

## File Structure (target)

```
app/
  Console/Kernel.php                       — schedules FetchGithubActivity hourly
  Filament/
    Resources/
      ProjectResource.php                  — tabbed form: Basic/Content/Media/Links
      SkillResource.php                    — simple; grouped by category
      ExperienceResource.php               — dates + "currently here"
      ServiceResource.php                  — simple; publish toggle
      ContactMessageResource.php           — read-only list + view
    Pages/Settings.php                     — Site/Home/About tabs (Spatie)
    Widgets/UnreadMessagesWidget.php
    Widgets/SiteHealthWidget.php
  Http/Middleware/*                        — Laravel defaults
  Jobs/FetchGithubActivity.php             — queued, hourly
  Livewire/
    Pages/Home.php
    Pages/About.php
    Pages/ProjectIndex.php                 — reactive tech-stack filter
    Pages/ProjectShow.php
    Pages/Services.php
    Pages/Contact.php                      — validated, honeypot, rate-limited
    Partials/SkillsMarquee.php
    Partials/GithubStrip.php
  Mail/ContactNotification.php
  Models/
    User.php                               — stock + is_admin
    Project.php
    Skill.php
    Experience.php
    Service.php
    ContactMessage.php
    GithubActivityCache.php                — single-row helper model
  Providers/Filament/AdminPanelProvider.php
  Settings/SiteSettings.php
  Settings/HomeSettings.php
  Settings/AboutSettings.php
database/
  factories/{Project,Skill,Experience,Service,ContactMessage}Factory.php
  migrations/<timestamp>_create_*.php      — one per table
  seeders/DatabaseSeeder.php
resources/
  css/app.css                              — Tailwind entry
  js/app.js                                — Livewire + Alpine
  views/
    layouts/app.blade.php                  — nav + footer + <livewire:* />
    livewire/pages/{home,about,project-index,project-show,services,contact}.blade.php
    livewire/partials/{skills-marquee,github-strip,theme-toggle}.blade.php
    errors/{404,500}.blade.php
tests/
  Feature/
    Pages/{HomePageTest,AboutPageTest,ProjectIndexTest,ProjectShowTest,ServicesPageTest,ContactFormTest}.php
    Admin/{AdminAccessTest,AdminResourceIndexTest}.php
  Unit/
    Jobs/FetchGithubActivityTest.php
tailwind.config.js                         — darkMode: 'class', custom fonts, typography plugin
vite.config.js                             — already created by Laravel; extend with Tailwind
config/settings.php                        — registers Spatie Settings classes
config/filament.php                        — generated
```

---

## Pre-flight: known environmental facts

- Existing `docker-compose.yml` defines `web`, `queue`, `mysql`, `redis`, `phpmyadmin`, `mailpit`. Web doc root is `/app/public`.
- `.env` is pre-seeded with Laravel-shaped keys (`APP_NAME=Portfolio`, `DB_HOST=mysql`, `DB_DATABASE=portfolio_db`, `REDIS_HOST=redis`, `MAIL_HOST=mailpit`). `APP_KEY` is empty and must be generated.
- The MySQL database name is `portfolio_db` but the container is named via `${CONTAINER_LABEL}_db` with env `MYSQL_DATABASE=${CONTAINER_LABEL}_db` → `portfolio_db`. Matches `.env`.
- A commented-out `scheduler` service already exists — we will uncomment it in Task 35.
- `entrypoint.sh` references `php artisan migrate` — it'll fail until Laravel is installed (Task 3).
- Current git state: on `main`, clean tracked tree, untracked Docker files (`Dockerfile`, `docker-compose.yml`, `entrypoint.sh`, `nginx-pwa.conf`, `.claude/`) that we'll commit in Task 1.
- Working directory on host: `/home/rrmmmjge/portfolio`. All shell commands below are run from there unless noted.

Shell convention used below:
- `dcr <svc> <cmd>` means `docker compose run --rm <svc> <cmd>` for one-shot container runs.
- `dce <svc> <cmd>` means `docker compose exec <svc> <cmd>` against a running container.
- If the containers aren't up yet, use `docker compose up -d` first.

---

## Phase 0 — Repo housekeeping

### Task 1: Commit the existing Docker scaffolding

**Files:**
- Modify: none (only staging existing untracked files)

- [ ] **Step 1: Review the untracked files**

Run:
```bash
git status
```
Expected: shows `.claude/`, `Dockerfile`, `docker-compose.yml`, `entrypoint.sh`, `nginx-pwa.conf` as untracked.

- [ ] **Step 2: Stage the Docker files (exclude `.claude/`)**

Run:
```bash
git add Dockerfile docker-compose.yml entrypoint.sh nginx-pwa.conf
```

- [ ] **Step 3: Verify `.gitignore` covers `.claude/`**

Run:
```bash
grep -E "^\.claude/?$|^\.claude/$" .gitignore || echo ".claude/" >> .gitignore
git add .gitignore
```
Expected: either grep finds `.claude/` already ignored, or we append it and stage `.gitignore`.

- [ ] **Step 4: Commit**

```bash
git commit -m "chore: add Docker scaffolding for Laravel portfolio"
```

### Task 2: Add `docs/superpowers/plans/` entry to git

**Files:**
- Create already exists: `docs/superpowers/plans/2026-04-13-portfolio-v1.md` (this file)

- [ ] **Step 1: Stage the plan document**

```bash
git add docs/superpowers/plans/2026-04-13-portfolio-v1.md
```

- [ ] **Step 2: Commit**

```bash
git commit -m "docs: add v1 implementation plan"
```

---

## Phase 1 — Laravel + Docker boot

### Task 3: Install Laravel 11 into the repo without clobbering existing files

**Files:**
- Create: `composer.json`, `composer.lock`, `artisan`, `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`, `.editorconfig`, `phpunit.xml`, `vite.config.js`, `package.json`, etc. — everything the Laravel skeleton ships with.
- Preserve: current `.env`, `.gitignore`, `Dockerfile`, `docker-compose.yml`, `entrypoint.sh`, `nginx-pwa.conf`, `docs/`, `.docker/`, `.git/`.

Strategy: install the Laravel skeleton into a temp directory, then copy non-conflicting files into the project. This avoids Composer's "directory is not empty" error and preserves local config.

- [ ] **Step 1: Make sure the Docker containers can build a PHP image we can reuse**

Run:
```bash
docker compose build web
```
Expected: image builds successfully (first build may be slow).

- [ ] **Step 2: Install Laravel 11 skeleton into a temp container path**

Run (uses the `composer:2` image so we don't need PHP on the host):
```bash
docker run --rm -v "$PWD":/host -w /tmp composer:2 \
  bash -lc "composer create-project --prefer-dist laravel/laravel:^11.0 /tmp/skeleton && \
            rsync -a --ignore-existing /tmp/skeleton/ /host/"
```
Expected: rsync adds `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`, `composer.json`, `composer.lock`, `artisan`, `phpunit.xml`, `vite.config.js`, `package.json`, `.editorconfig`, `README.md`, `.gitattributes`. Existing `.env`, `.gitignore`, Dockerfile, etc. are preserved because of `--ignore-existing`.

- [ ] **Step 3: Fix ownership (rsync inside the container may leave root-owned files)**

Run:
```bash
sudo chown -R "$USER:$USER" .
```
Expected: all files owned by current user.

- [ ] **Step 4: Verify the skeleton landed and our `.env` was preserved**

Run:
```bash
test -f artisan && test -f composer.json && grep -q "APP_NAME=Portfolio" .env && echo OK
```
Expected: `OK`.

- [ ] **Step 5: Install composer dependencies inside a Laravel-shaped container**

Run:
```bash
docker run --rm -v "$PWD":/app -w /app composer:2 composer install --no-interaction
```
Expected: `vendor/` populated; no errors.

- [ ] **Step 6: Generate APP_KEY**

Bring up the web container and generate:
```bash
docker compose up -d web
docker compose exec web php artisan key:generate
```
Expected: `APP_KEY=base64:...` now set in `.env`.

- [ ] **Step 7: Smoke-run the welcome page**

Run:
```bash
curl -sS -o /dev/null -w "%{http_code}\n" http://localhost:8080/
```
Expected: `200`.

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: install Laravel 11 skeleton"
```

### Task 4: Run Laravel's default migrations against the MySQL service

**Files:**
- Modify: none (uses stock Laravel migrations)

- [ ] **Step 1: Run migrations**

```bash
docker compose exec web php artisan migrate --force
```
Expected: `users`, `password_reset_tokens`, `sessions`, `cache`, `jobs` tables created.

- [ ] **Step 2: Verify in MySQL**

```bash
docker compose exec mysql mysql -uroot -ppassword -e "SHOW TABLES FROM portfolio_db;"
```
Expected: lists the five stock tables.

- [ ] **Step 3: Commit (if any artifacts changed, e.g., `composer.lock`)**

```bash
git status
git add -A
git diff --cached --quiet || git commit -m "chore: run default Laravel migrations"
```

### Task 5: Switch session/cache/queue to Redis

**Files:**
- Modify: `.env`

- [ ] **Step 1: Update three env lines**

Edit `.env` (exact replacements):
```
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```
(Replaces `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`.)

- [ ] **Step 2: Clear any cached config**

```bash
docker compose exec web php artisan config:clear
```

- [ ] **Step 3: Verify Redis connectivity from the app**

```bash
docker compose exec web php artisan tinker --execute="echo Cache::put('plan-ping','pong',60) ? Cache::get('plan-ping') : 'fail';"
```
Expected: prints `pong`.

- [ ] **Step 4: Commit**

```bash
git add .env
git commit -m "chore: move session/cache/queue to Redis"
```

### Task 6: Install Pest and confirm it runs

**Files:**
- Modify: `composer.json`, `tests/Pest.php`, `tests/TestCase.php`

- [ ] **Step 1: Install Pest v2 and its Laravel plugin**

```bash
docker compose exec web composer require --dev "pestphp/pest:^2.0" "pestphp/pest-plugin-laravel:^2.0"
docker compose exec web ./vendor/bin/pest --init
```
Expected: `tests/Pest.php` created, `phpunit.xml` updated.

- [ ] **Step 2: Write a trivial failing test (to confirm plumbing)**

Create `tests/Feature/SmokeTest.php`:
```php
<?php

it('has a working framework', function () {
    expect(true)->toBeTrue();
});
```

- [ ] **Step 3: Run it**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/SmokeTest.php
```
Expected: `PASS`.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "chore: install Pest, add smoke test"
```

---

## Phase 2 — Frontend toolchain & base layout

### Task 7: Install Tailwind 3 + typography plugin and configure fonts

**Files:**
- Modify: `package.json`, `tailwind.config.js`, `resources/css/app.css`, `resources/views/layouts/app.blade.php` (created here)
- Create: `tailwind.config.js` (Laravel 11 doesn't ship one by default on some Vite starters — if it exists, overwrite)

- [ ] **Step 1: Install Node deps**

```bash
docker compose exec web npm install -D tailwindcss@^3 postcss autoprefixer @tailwindcss/typography
docker compose exec web npx tailwindcss init -p
```
Expected: `tailwind.config.js` and `postcss.config.js` created; Tailwind on v3.

- [ ] **Step 2: Write `tailwind.config.js`**

Replace the generated `tailwind.config.js` with:
```js
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                serif: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
            },
        },
    },
    plugins: [require('@tailwindcss/typography')],
};
```

- [ ] **Step 3: Replace `resources/css/app.css` with Tailwind entry and font imports**

```css
@import url('https://fonts.bunny.net/css?family=fraunces:400,500,600|inter:400,500,600,700|jetbrains-mono:400,500&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    html { scroll-behavior: smooth; }
    body { @apply font-sans bg-white text-neutral-900 antialiased; }
    .dark body { @apply bg-neutral-950 text-neutral-100; }
    h1, h2, h3, h4 { @apply font-serif tracking-tight; }
    code, pre, .mono { @apply font-mono; }
}
```

- [ ] **Step 4: Build once to confirm the pipeline**

```bash
docker compose exec web npm run build
```
Expected: `public/build/manifest.json` and compiled CSS appear.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: configure Tailwind 3 with Bunny fonts and typography plugin"
```

### Task 8: Install Livewire 3 and create the shared layout

**Files:**
- Modify: `composer.json`
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/livewire/partials/theme-toggle.blade.php`

- [ ] **Step 1: Install Livewire 3**

```bash
docker compose exec web composer require "livewire/livewire:^3.0"
```

- [ ] **Step 2: Create `resources/views/layouts/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ dark: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('dark', v => { localStorage.theme = v ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <header class="max-w-5xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="/" wire:navigate class="font-serif text-xl font-semibold tracking-tight">{{ config('app.name') }}</a>
        <nav class="flex items-center gap-6 text-sm">
            <a href="/" wire:navigate class="hover:underline">Home</a>
            <a href="/about" wire:navigate class="hover:underline">About</a>
            <a href="/projects" wire:navigate class="hover:underline">Projects</a>
            <a href="/services" wire:navigate class="hover:underline">Services</a>
            <a href="/contact" wire:navigate class="hover:underline">Contact</a>
            @include('livewire.partials.theme-toggle')
        </nav>
    </header>

    <main class="max-w-5xl mx-auto px-6 pb-20">
        {{ $slot }}
    </main>

    <footer class="max-w-5xl mx-auto px-6 py-8 text-xs text-neutral-500 border-t border-neutral-200 dark:border-neutral-800">
        <div class="flex justify-between items-center">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
            <span>Built with Laravel</span>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
```

- [ ] **Step 3: Create `resources/views/livewire/partials/theme-toggle.blade.php`**

```blade
<button type="button" @click="dark = !dark" :aria-pressed="dark" class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800 transition" aria-label="Toggle theme">
    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.95 2.05a1 1 0 010 1.41l-.71.71a1 1 0 11-1.41-1.41l.71-.71a1 1 0 011.41 0zM18 9a1 1 0 110 2h-1a1 1 0 110-2h1zM5.05 4.05a1 1 0 011.41 0l.71.71A1 1 0 115.76 6.17l-.71-.71a1 1 0 010-1.41zM3 9a1 1 0 110 2H2a1 1 0 110-2h1zm7 4a3 3 0 100-6 3 3 0 000 6zm4.24 2.24l.71.71a1 1 0 01-1.41 1.41l-.71-.71a1 1 0 011.41-1.41zM10 17a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM4.34 15.24a1 1 0 010-1.41l.71-.71A1 1 0 116.46 14.54l-.71.71a1 1 0 01-1.41 0z"/></svg>
</button>
```

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: add base layout with theme toggle and Livewire navigation"
```

### Task 9: Wire Alpine's `x-cloak` and the bundled Livewire JS

**Files:**
- Modify: `resources/css/app.css`, `resources/js/app.js`

- [ ] **Step 1: Add `x-cloak` CSS**

Append to `resources/css/app.css`:
```css
[x-cloak] { display: none !important; }
```

- [ ] **Step 2: Ensure `resources/js/app.js` is minimal (Livewire brings Alpine)**

Replace `resources/js/app.js` with:
```js
import './bootstrap';
```
(Laravel creates `resources/js/bootstrap.js` — leave it alone.)

- [ ] **Step 3: Rebuild assets**

```bash
docker compose exec web npm run build
```
Expected: manifest updates without errors.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: enable x-cloak and baseline JS bootstrap"
```

### Task 10: Write a feature test for the shared layout (TDD anchor)

**Files:**
- Create: `tests/Feature/LayoutTest.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/LayoutTest.php`:
```php
<?php

it('renders the shared layout on the welcome route', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('Portfolio', false);
    $response->assertSee('Home', false);
    $response->assertSee('Contact', false);
});
```

- [ ] **Step 2: Run it**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/LayoutTest.php
```
Expected: FAIL (welcome route still renders Laravel's stock welcome blade).

- [ ] **Step 3: Replace `routes/web.php` with a placeholder that uses the layout**

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home-placeholder')->name('home');
```

- [ ] **Step 4: Create `resources/views/home-placeholder.blade.php`**

```blade
<x-layouts.app>
    <h1 class="text-4xl mt-8">Portfolio (placeholder)</h1>
</x-layouts.app>
```

- [ ] **Step 5: Promote the layout to a blade component — rename `resources/views/layouts/app.blade.php` to `resources/views/components/layouts/app.blade.php`**

```bash
mkdir -p resources/views/components/layouts
git mv resources/views/layouts/app.blade.php resources/views/components/layouts/app.blade.php
```

- [ ] **Step 6: Re-run the test**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/LayoutTest.php
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: promote app layout to x-layouts.app component + placeholder home"
```

---

## Phase 3 — Data model, settings, factories, seeder

### Task 11: Users table: add `is_admin` + seed an admin

**Files:**
- Create: migration `database/migrations/<ts>_add_is_admin_to_users_table.php`
- Modify: `app/Models/User.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `tests/Feature/UserIsAdminTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/UserIsAdminTest.php`:
```php
<?php

use App\Models\User;

it('stores and exposes is_admin as boolean', function () {
    $user = User::factory()->create(['is_admin' => true]);
    expect($user->fresh()->is_admin)->toBeTrue();
});
```

- [ ] **Step 2: Run it**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/UserIsAdminTest.php
```
Expected: FAIL (`is_admin` column does not exist).

- [ ] **Step 3: Create the migration**

```bash
docker compose exec web php artisan make:migration add_is_admin_to_users_table --table=users
```

Replace its `up`/`down` with:
```php
public function up(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false)->after('password');
    });
}

public function down(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_admin');
    });
}
```

- [ ] **Step 4: Update `app/Models/User.php`**

Add to `$fillable`:
```php
protected $fillable = ['name', 'email', 'password', 'is_admin'];
```
Add to `casts()`:
```php
protected function casts(): array {
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];
}
```

- [ ] **Step 5: Update the UserFactory — add `is_admin` default**

In `database/factories/UserFactory.php`, add inside `definition()`:
```php
'is_admin' => false,
```

- [ ] **Step 6: Run migrations + test**

```bash
docker compose exec web php artisan migrate --force
docker compose exec web ./vendor/bin/pest tests/Feature/UserIsAdminTest.php
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(users): add is_admin flag with factory and model cast"
```

### Task 12: Projects migration, model, factory, tests

**Files:**
- Create: `database/migrations/<ts>_create_projects_table.php`
- Create: `app/Models/Project.php`
- Create: `database/factories/ProjectFactory.php`
- Create: `tests/Feature/ProjectModelTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/ProjectModelTest.php`:
```php
<?php

use App\Models\Project;

it('creates a project with tech stack as an array and slug unique', function () {
    $p = Project::factory()->create([
        'slug' => 'laravel-billing',
        'tech_stack' => ['Laravel', 'MySQL', 'Redis'],
    ]);

    expect($p->fresh()->tech_stack)->toBe(['Laravel', 'MySQL', 'Redis'])
        ->and(Project::where('slug', 'laravel-billing')->count())->toBe(1);
});

it('filters published and featured', function () {
    Project::factory()->create(['is_published' => false]);
    Project::factory()->create(['is_published' => true, 'is_featured' => true]);
    Project::factory()->create(['is_published' => true, 'is_featured' => false]);

    expect(Project::published()->count())->toBe(2)
        ->and(Project::published()->featured()->count())->toBe(1);
});
```

- [ ] **Step 2: Run it**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/ProjectModelTest.php
```
Expected: FAIL (class `Project` not found).

- [ ] **Step 3: Create migration**

```bash
docker compose exec web php artisan make:migration create_projects_table
```

Replace with:
```php
public function up(): void {
    Schema::create('projects', function (Blueprint $table) {
        $table->id();
        $table->string('slug')->unique();
        $table->string('title');
        $table->string('summary');
        $table->string('role')->nullable();
        $table->date('started_at')->nullable();
        $table->date('ended_at')->nullable();
        $table->json('tech_stack')->nullable();
        $table->string('cover_image_path')->nullable();
        $table->string('architecture_image_path')->nullable();
        $table->longText('problem')->nullable();
        $table->longText('approach')->nullable();
        $table->longText('challenges')->nullable();
        $table->longText('outcome')->nullable();
        $table->string('repo_url')->nullable();
        $table->string('demo_url')->nullable();
        $table->boolean('is_featured')->default(false);
        $table->unsignedInteger('sort_order')->default(0);
        $table->boolean('is_published')->default(false);
        $table->timestamps();

        $table->index(['is_published', 'is_featured', 'sort_order']);
    });
}

public function down(): void { Schema::dropIfExists('projects'); }
```

- [ ] **Step 4: Create `app/Models/Project.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array {
        return [
            'tech_stack' => 'array',
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder {
        return $q->where('is_published', true);
    }

    public function scopeFeatured(Builder $q): Builder {
        return $q->where('is_featured', true);
    }

    public function scopeOrdered(Builder $q): Builder {
        return $q->orderBy('sort_order')->orderByDesc('started_at');
    }
}
```

- [ ] **Step 5: Create `database/factories/ProjectFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array {
        $title = $this->faker->unique()->sentence(3);
        return [
            'slug' => Str::slug($title),
            'title' => rtrim($title, '.'),
            'summary' => $this->faker->sentence(12),
            'role' => 'Backend engineer',
            'started_at' => now()->subMonths(rand(3, 24)),
            'ended_at' => now()->subMonths(rand(0, 2)),
            'tech_stack' => $this->faker->randomElements(['Laravel', 'PHP', 'MySQL', 'Redis', 'Livewire', 'Filament', 'Docker'], 4),
            'problem' => '<p>' . $this->faker->paragraph() . '</p>',
            'approach' => '<p>' . $this->faker->paragraph() . '</p>',
            'challenges' => '<p>' . $this->faker->paragraph() . '</p>',
            'outcome' => '<p>' . $this->faker->paragraph() . '</p>',
            'repo_url' => 'https://github.com/example/' . Str::slug($title),
            'demo_url' => null,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
```

- [ ] **Step 6: Migrate + run tests**

```bash
docker compose exec web php artisan migrate --force
docker compose exec web ./vendor/bin/pest tests/Feature/ProjectModelTest.php
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(projects): add schema, model, factory"
```

### Task 13: Skills migration, model, factory, tests

**Files:**
- Create: `database/migrations/<ts>_create_skills_table.php`
- Create: `app/Models/Skill.php`
- Create: `database/factories/SkillFactory.php`
- Create: `tests/Feature/SkillModelTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Skill;

it('creates a skill with an enum category', function () {
    $s = Skill::factory()->create(['category' => 'framework']);
    expect($s->fresh()->category)->toBe('framework');
});

it('scopes ordered results', function () {
    Skill::factory()->create(['sort_order' => 2, 'name' => 'B']);
    Skill::factory()->create(['sort_order' => 1, 'name' => 'A']);
    expect(Skill::ordered()->pluck('name')->all())->toBe(['A', 'B']);
});
```

- [ ] **Step 2: Run — expect FAIL (model missing)**

```bash
docker compose exec web ./vendor/bin/pest tests/Feature/SkillModelTest.php
```

- [ ] **Step 3: Migration**

```php
public function up(): void {
    Schema::create('skills', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('category', ['language', 'framework', 'database', 'tool', 'platform']);
        $table->string('icon')->nullable();
        $table->unsignedTinyInteger('proficiency')->default(3);
        $table->unsignedInteger('sort_order')->default(0);
        $table->timestamps();

        $table->index(['category', 'sort_order']);
    });
}

public function down(): void { Schema::dropIfExists('skills'); }
```

- [ ] **Step 4: `app/Models/Skill.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeOrdered(Builder $q): Builder {
        return $q->orderBy('sort_order')->orderBy('name');
    }
}
```

- [ ] **Step 5: `database/factories/SkillFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array {
        $name = $this->faker->unique()->word();
        return [
            'name' => ucfirst($name),
            'category' => $this->faker->randomElement(['language', 'framework', 'database', 'tool', 'platform']),
            'icon' => 'devicon-laravel-plain',
            'proficiency' => rand(3, 5),
            'sort_order' => 0,
        ];
    }
}
```

- [ ] **Step 6: Migrate + run**

```bash
docker compose exec web php artisan migrate --force
docker compose exec web ./vendor/bin/pest tests/Feature/SkillModelTest.php
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(skills): schema, model, factory"
```

### Task 14: Experiences migration, model, factory, tests

**Files:**
- Create: `database/migrations/<ts>_create_experiences_table.php`
- Create: `app/Models/Experience.php`
- Create: `database/factories/ExperienceFactory.php`
- Create: `tests/Feature/ExperienceModelTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Experience;

it('treats null ended_at as "present"', function () {
    $e = Experience::factory()->create(['ended_at' => null]);
    expect($e->fresh()->ended_at)->toBeNull();
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Migration**

```php
public function up(): void {
    Schema::create('experiences', function (Blueprint $table) {
        $table->id();
        $table->string('company');
        $table->string('role');
        $table->string('location')->nullable();
        $table->date('started_at');
        $table->date('ended_at')->nullable();
        $table->longText('description')->nullable();
        $table->string('logo_path')->nullable();
        $table->unsignedInteger('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void { Schema::dropIfExists('experiences'); }
```

- [ ] **Step 4: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array {
        return ['started_at' => 'date', 'ended_at' => 'date'];
    }

    public function scopeOrdered(Builder $q): Builder {
        return $q->orderBy('sort_order')->orderByDesc('started_at');
    }
}
```

- [ ] **Step 5: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array {
        return [
            'company' => $this->faker->company(),
            'role' => 'Backend Engineer',
            'location' => 'Remote',
            'started_at' => now()->subYears(2),
            'ended_at' => now()->subMonths(3),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'sort_order' => 0,
        ];
    }
}
```

- [ ] **Step 6: Migrate + run — PASS**

```bash
docker compose exec web php artisan migrate --force
docker compose exec web ./vendor/bin/pest tests/Feature/ExperienceModelTest.php
```

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(experiences): schema, model, factory"
```

### Task 15: Services migration, model, factory, tests

**Files:**
- Create: `database/migrations/<ts>_create_services_table.php`
- Create: `app/Models/Service.php`
- Create: `database/factories/ServiceFactory.php`
- Create: `tests/Feature/ServiceModelTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Service;

it('scopes published services', function () {
    Service::factory()->create(['is_published' => false]);
    Service::factory()->create(['is_published' => true]);
    expect(Service::published()->count())->toBe(1);
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Migration**

```php
public function up(): void {
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->longText('description')->nullable();
        $table->string('icon')->nullable();
        $table->string('starting_price')->nullable();
        $table->unsignedInteger('sort_order')->default(0);
        $table->boolean('is_published')->default(false);
        $table->timestamps();
    });
}

public function down(): void { Schema::dropIfExists('services'); }
```

- [ ] **Step 4: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array { return ['is_published' => 'boolean']; }

    public function scopePublished(Builder $q): Builder {
        return $q->where('is_published', true);
    }

    public function scopeOrdered(Builder $q): Builder {
        return $q->orderBy('sort_order');
    }
}
```

- [ ] **Step 5: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array {
        return [
            'title' => $this->faker->sentence(3),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'icon' => 'devicon-laravel-plain',
            'starting_price' => 'From $1,500',
            'sort_order' => 0,
            'is_published' => true,
        ];
    }
}
```

- [ ] **Step 6: Migrate + run — PASS**

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(services): schema, model, factory"
```

### Task 16: Contact messages migration, model, factory, tests

**Files:**
- Create: migration `<ts>_create_contact_messages_table.php`
- Create: `app/Models/ContactMessage.php`
- Create: `database/factories/ContactMessageFactory.php`
- Create: `tests/Feature/ContactMessageModelTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\ContactMessage;

it('scopes unread messages', function () {
    ContactMessage::factory()->create(['is_read' => true]);
    ContactMessage::factory()->count(2)->create(['is_read' => false]);
    expect(ContactMessage::unread()->count())->toBe(2);
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Migration**

```php
public function up(): void {
    Schema::create('contact_messages', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('subject');
        $table->text('message');
        $table->string('ip_address', 45)->nullable();
        $table->string('user_agent')->nullable();
        $table->boolean('is_read')->default(false);
        $table->timestamp('created_at')->useCurrent();

        $table->index(['is_read', 'created_at']);
    });
}

public function down(): void { Schema::dropIfExists('contact_messages'); }
```

- [ ] **Step 4: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array {
        return ['is_read' => 'boolean', 'created_at' => 'datetime'];
    }

    public function scopeUnread(Builder $q): Builder {
        return $q->where('is_read', false);
    }
}
```

- [ ] **Step 5: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => 'Mozilla/5.0 factory',
            'is_read' => false,
            'created_at' => now(),
        ];
    }
}
```

- [ ] **Step 6: Migrate + run — PASS**

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(contact): schema, model, factory"
```

### Task 17: GitHub activity cache table + model

**Files:**
- Create: migration `<ts>_create_github_activity_cache_table.php`
- Create: `app/Models/GithubActivityCache.php`
- Create: `tests/Feature/GithubActivityCacheTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\GithubActivityCache;

it('returns the single latest cache row or null', function () {
    expect(GithubActivityCache::latestSnapshot())->toBeNull();

    GithubActivityCache::create([
        'payload' => ['events' => []],
        'fetched_at' => now(),
    ]);

    expect(GithubActivityCache::latestSnapshot())->not->toBeNull();
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Migration**

```php
public function up(): void {
    Schema::create('github_activity_cache', function (Blueprint $table) {
        $table->id();
        $table->json('payload');
        $table->timestamp('fetched_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void { Schema::dropIfExists('github_activity_cache'); }
```

- [ ] **Step 4: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GithubActivityCache extends Model
{
    protected $table = 'github_activity_cache';

    protected $guarded = ['id'];

    protected function casts(): array {
        return ['payload' => 'array', 'fetched_at' => 'datetime'];
    }

    public static function latestSnapshot(): ?self {
        return static::orderByDesc('fetched_at')->first();
    }
}
```

- [ ] **Step 5: Migrate + run — PASS**

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(github-cache): single-row cache model"
```

### Task 18: Spatie Settings + seeder

**Files:**
- Modify: `composer.json`
- Create: `app/Settings/SiteSettings.php`, `HomeSettings.php`, `AboutSettings.php`
- Create: migration `<ts>_create_settings_table.php` (via `php artisan settings:table` — name differs)
- Create: setting migrations in `database/settings/` for initial defaults
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `tests/Feature/SettingsTest.php`

- [ ] **Step 1: Install Spatie Settings**

```bash
docker compose exec web composer require "spatie/laravel-settings:^3.0"
docker compose exec web php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"
docker compose exec web php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="settings"
docker compose exec web php artisan migrate --force
```

- [ ] **Step 2: Create `app/Settings/SiteSettings.php`**

```php
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $site_title;
    public string $tagline;
    public string $meta_description;
    public ?string $github_url;
    public ?string $linkedin_url;
    public ?string $twitter_url;
    public string $email;
    public string $footer_text;
    public bool $is_available_for_work;

    public static function group(): string { return 'site'; }
}
```

- [ ] **Step 3: Create `HomeSettings.php`**

```php
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $hero_headline;
    public string $hero_subheadline;
    public string $hero_cta_text;
    public string $hero_cta_url;

    public static function group(): string { return 'home'; }
}
```

- [ ] **Step 4: Create `AboutSettings.php`**

```php
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public string $bio;
    public ?string $profile_image_path;
    public ?string $cv_pdf_path;
    public ?string $location;
    public ?int $years_experience;

    public static function group(): string { return 'about'; }
}
```

- [ ] **Step 5: Register them in `config/settings.php`**

Under `'settings' => [ ... ]`, add:
```php
\App\Settings\SiteSettings::class,
\App\Settings\HomeSettings::class,
\App\Settings\AboutSettings::class,
```

- [ ] **Step 6: Create `database/settings/<ts>_create_site_home_about_settings.php`**

```php
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.site_title', 'Portfolio');
        $this->migrator->add('site.tagline', 'Backend engineer building reliable Laravel systems.');
        $this->migrator->add('site.meta_description', 'Laravel/PHP backend engineer portfolio.');
        $this->migrator->add('site.github_url', null);
        $this->migrator->add('site.linkedin_url', null);
        $this->migrator->add('site.twitter_url', null);
        $this->migrator->add('site.email', 'hello@example.com');
        $this->migrator->add('site.footer_text', 'Built with Laravel');
        $this->migrator->add('site.is_available_for_work', true);

        $this->migrator->add('home.hero_headline', 'Backend engineer who ships.');
        $this->migrator->add('home.hero_subheadline', 'I build reliable Laravel systems for teams that need things to work.');
        $this->migrator->add('home.hero_cta_text', 'See selected work');
        $this->migrator->add('home.hero_cta_url', '/projects');

        $this->migrator->add('about.bio', '<p>Backend engineer with a focus on Laravel, queues, and data integrity.</p>');
        $this->migrator->add('about.profile_image_path', null);
        $this->migrator->add('about.cv_pdf_path', null);
        $this->migrator->add('about.location', 'Remote');
        $this->migrator->add('about.years_experience', 5);
    }
};
```

- [ ] **Step 7: Failing test**

`tests/Feature/SettingsTest.php`:
```php
<?php

use App\Settings\SiteSettings;

it('loads site settings with seeded defaults', function () {
    $settings = app(SiteSettings::class);
    expect($settings->site_title)->toBe('Portfolio')
        ->and($settings->is_available_for_work)->toBeTrue();
});
```

- [ ] **Step 8: Migrate + run — PASS**

```bash
docker compose exec web php artisan migrate --force
docker compose exec web ./vendor/bin/pest tests/Feature/SettingsTest.php
```

- [ ] **Step 9: Update `database/seeders/DatabaseSeeder.php`**

Replace with:
```php
<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Site Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        Project::factory()->count(4)->create(['is_featured' => true]);
        Project::factory()->count(6)->create();

        foreach (['language', 'framework', 'database', 'tool', 'platform'] as $cat) {
            Skill::factory()->count(3)->create(['category' => $cat]);
        }

        Experience::factory()->count(3)->create();
        Service::factory()->count(4)->create();
    }
}
```

- [ ] **Step 10: Run seeder to validate it**

```bash
docker compose exec web php artisan db:seed --force
```
Expected: no errors; admin + demo content inserted.

- [ ] **Step 11: Commit**

```bash
git add -A
git commit -m "feat(settings): spatie settings + seed initial content"
```

---

## Phase 4 — Filament admin

### Task 19: Install Filament v3 and bootstrap an admin panel

**Files:**
- Modify: `composer.json`
- Create: `app/Providers/Filament/AdminPanelProvider.php`
- Modify: `bootstrap/providers.php` (Laravel 11 provider registry)

- [ ] **Step 1: Install**

```bash
docker compose exec web composer require "filament/filament:^3.2"
docker compose exec web php artisan filament:install --panels --no-interaction
```
When prompted for panel id, accept `admin`.

- [ ] **Step 2: Protect admin access — edit `app/Providers/Filament/AdminPanelProvider.php`**

In `panel()` call chain, confirm `->authGuard('web')` and `->login()`. Add:
```php
->authMiddleware([
    \Filament\Http\Middleware\Authenticate::class,
])
```

- [ ] **Step 3: Register admin check on the User model**

Add to `app/Models/User.php`:
```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    // existing code...

    public function canAccessPanel(Panel $panel): bool {
        return $this->is_admin === true;
    }
}
```

- [ ] **Step 4: Smoke-test admin login redirect**

```bash
curl -sS -o /dev/null -w "%{http_code} %{redirect_url}\n" http://localhost:8080/admin
```
Expected: `302` redirect to `/admin/login`.

- [ ] **Step 5: Feature test — non-admin rejected**

`tests/Feature/Admin/AdminAccessTest.php`:
```php
<?php

use App\Models\User;

it('redirects guests from /admin', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('blocks non-admin users', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('allows admin users', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin')->assertOk();
});
```

Run — PASS.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(admin): install Filament panel with is_admin gate"
```

### Task 20: ProjectResource with tabbed form

**Files:**
- Create: `app/Filament/Resources/ProjectResource.php`
- Create: `app/Filament/Resources/ProjectResource/Pages/*.php` (generated)

- [ ] **Step 1: Generate skeleton**

```bash
docker compose exec web php artisan make:filament-resource Project --generate --no-interaction
```

- [ ] **Step 2: Replace `app/Filament/Resources/ProjectResource.php` form with tabs**

In the `form()` method:
```php
public static function form(Form $form): Form {
    return $form->schema([
        \Filament\Forms\Components\Tabs::make()->tabs([
            \Filament\Forms\Components\Tabs\Tab::make('Basic')->schema([
                \Filament\Forms\Components\TextInput::make('title')->required()->maxLength(150)->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                \Filament\Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(160),
                \Filament\Forms\Components\TextInput::make('summary')->required()->maxLength(255),
                \Filament\Forms\Components\TextInput::make('role')->maxLength(100),
                \Filament\Forms\Components\DatePicker::make('started_at'),
                \Filament\Forms\Components\DatePicker::make('ended_at'),
                \Filament\Forms\Components\TagsInput::make('tech_stack')->placeholder('Laravel, MySQL, Redis'),
                \Filament\Forms\Components\Toggle::make('is_featured'),
                \Filament\Forms\Components\Toggle::make('is_published'),
                \Filament\Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
            \Filament\Forms\Components\Tabs\Tab::make('Content')->schema([
                \Filament\Forms\Components\RichEditor::make('problem')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                \Filament\Forms\Components\RichEditor::make('approach')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                \Filament\Forms\Components\RichEditor::make('challenges')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                \Filament\Forms\Components\RichEditor::make('outcome')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
            ]),
            \Filament\Forms\Components\Tabs\Tab::make('Media')->schema([
                \Filament\Forms\Components\FileUpload::make('cover_image_path')->image()->disk('public')->directory('projects/covers')->maxSize(5120)->acceptedFileTypes(['image/jpeg','image/png','image/webp']),
                \Filament\Forms\Components\FileUpload::make('architecture_image_path')->image()->disk('public')->directory('projects/architecture')->maxSize(5120)->acceptedFileTypes(['image/jpeg','image/png','image/webp']),
            ]),
            \Filament\Forms\Components\Tabs\Tab::make('Links')->schema([
                \Filament\Forms\Components\TextInput::make('repo_url')->url()->maxLength(255),
                \Filament\Forms\Components\TextInput::make('demo_url')->url()->maxLength(255),
            ]),
        ])->columnSpanFull(),
    ]);
}
```

- [ ] **Step 3: Table with drag-reorder and publish badge**

Replace the table method:
```php
public static function table(Table $table): Table {
    return $table
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            \Filament\Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
            \Filament\Tables\Columns\IconColumn::make('is_published')->boolean()->label('Published'),
            \Filament\Tables\Columns\TextColumn::make('updated_at')->since()->label('Updated'),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ]);
}
```

- [ ] **Step 4: Run the symlink for public uploads**

```bash
docker compose exec web php artisan storage:link
```

- [ ] **Step 5: Admin resource index test**

`tests/Feature/Admin/AdminResourceIndexTest.php`:
```php
<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

it('loads the project index', function () {
    $this->get('/admin/projects')->assertOk();
});
```

Run — PASS.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(admin): ProjectResource with tabbed form, drag-reorder, media uploads"
```

### Task 21: SkillResource, ExperienceResource, ServiceResource

**Files:**
- Create: `app/Filament/Resources/SkillResource.php`
- Create: `app/Filament/Resources/ExperienceResource.php`
- Create: `app/Filament/Resources/ServiceResource.php`
- Modify: `tests/Feature/Admin/AdminResourceIndexTest.php`

- [ ] **Step 1: Generate resources**

```bash
docker compose exec web php artisan make:filament-resource Skill --generate --no-interaction
docker compose exec web php artisan make:filament-resource Experience --generate --no-interaction
docker compose exec web php artisan make:filament-resource Service --generate --no-interaction
```

- [ ] **Step 2: SkillResource form**

Replace `form()`:
```php
public static function form(Form $form): Form {
    return $form->schema([
        \Filament\Forms\Components\TextInput::make('name')->required()->maxLength(80),
        \Filament\Forms\Components\Select::make('category')->required()->options([
            'language' => 'Language', 'framework' => 'Framework',
            'database' => 'Database', 'tool' => 'Tool', 'platform' => 'Platform',
        ]),
        \Filament\Forms\Components\TextInput::make('icon')->helperText('devicon class, e.g., devicon-laravel-plain')->maxLength(100),
        \Filament\Forms\Components\TextInput::make('proficiency')->numeric()->minValue(1)->maxValue(5)->default(3),
        \Filament\Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
    ]);
}
```

Table:
```php
public static function table(Table $table): Table {
    return $table
        ->reorderable('sort_order')
        ->groups(['category'])
        ->defaultGroup('category')
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('name')->searchable(),
            \Filament\Tables\Columns\TextColumn::make('category'),
            \Filament\Tables\Columns\TextColumn::make('proficiency'),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ]);
}
```

- [ ] **Step 3: ExperienceResource form**

```php
public static function form(Form $form): Form {
    return $form->schema([
        \Filament\Forms\Components\TextInput::make('company')->required()->maxLength(120),
        \Filament\Forms\Components\TextInput::make('role')->required()->maxLength(120),
        \Filament\Forms\Components\TextInput::make('location')->maxLength(120),
        \Filament\Forms\Components\DatePicker::make('started_at')->required(),
        \Filament\Forms\Components\Toggle::make('currently_here')->dehydrated(false)->live()->afterStateUpdated(fn ($state, $set) => $state ? $set('ended_at', null) : null),
        \Filament\Forms\Components\DatePicker::make('ended_at')->visible(fn ($get) => ! $get('currently_here')),
        \Filament\Forms\Components\RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'bulletList', 'link'])->columnSpanFull(),
        \Filament\Forms\Components\FileUpload::make('logo_path')->image()->disk('public')->directory('experiences/logos')->maxSize(5120)->acceptedFileTypes(['image/jpeg','image/png','image/webp']),
        \Filament\Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
    ]);
}
```

Table:
```php
public static function table(Table $table): Table {
    return $table
        ->reorderable('sort_order')
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('company')->searchable(),
            \Filament\Tables\Columns\TextColumn::make('role'),
            \Filament\Tables\Columns\TextColumn::make('started_at')->date(),
            \Filament\Tables\Columns\TextColumn::make('ended_at')->date()->placeholder('Present'),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ]);
}
```

- [ ] **Step 4: ServiceResource form**

```php
public static function form(Form $form): Form {
    return $form->schema([
        \Filament\Forms\Components\TextInput::make('title')->required()->maxLength(120),
        \Filament\Forms\Components\RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'bulletList', 'link'])->columnSpanFull(),
        \Filament\Forms\Components\TextInput::make('icon')->maxLength(100),
        \Filament\Forms\Components\TextInput::make('starting_price')->maxLength(60),
        \Filament\Forms\Components\Toggle::make('is_published'),
        \Filament\Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
    ]);
}
```

- [ ] **Step 5: Expand admin index test**

Replace `AdminResourceIndexTest.php` with:
```php
<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

it('loads all resource index pages', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/admin/projects',
    '/admin/skills',
    '/admin/experiences',
    '/admin/services',
]);
```

Run — PASS.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(admin): Skill, Experience, Service resources"
```

### Task 22: ContactMessageResource (read-only)

**Files:**
- Create: `app/Filament/Resources/ContactMessageResource.php`

- [ ] **Step 1: Generate and scope**

```bash
docker compose exec web php artisan make:filament-resource ContactMessage --generate --no-interaction
```

- [ ] **Step 2: Replace the resource to be read-only**

Entire file body (keep namespace + imports as generated, then):
```php
public static function form(Form $form): Form {
    return $form->schema([
        \Filament\Forms\Components\TextInput::make('name')->disabled(),
        \Filament\Forms\Components\TextInput::make('email')->disabled(),
        \Filament\Forms\Components\TextInput::make('subject')->disabled(),
        \Filament\Forms\Components\Textarea::make('message')->disabled()->rows(8)->columnSpanFull(),
        \Filament\Forms\Components\TextInput::make('ip_address')->disabled(),
        \Filament\Forms\Components\DateTimePicker::make('created_at')->disabled(),
    ]);
}

public static function table(Table $table): Table {
    return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            \Filament\Tables\Columns\IconColumn::make('is_read')->boolean()->label('Read'),
            \Filament\Tables\Columns\TextColumn::make('name')->searchable(),
            \Filament\Tables\Columns\TextColumn::make('email')->searchable(),
            \Filament\Tables\Columns\TextColumn::make('subject')->limit(40)->searchable(),
            \Filament\Tables\Columns\TextColumn::make('created_at')->since(),
        ])
        ->filters([
            \Filament\Tables\Filters\TernaryFilter::make('is_read')->label('Read status'),
        ])
        ->actions([
            \Filament\Tables\Actions\Action::make('view')->icon('heroicon-o-eye')->url(fn ($record) => static::getUrl('view', ['record' => $record])),
            \Filament\Tables\Actions\Action::make('markRead')->icon('heroicon-o-check')->visible(fn ($record) => ! $record->is_read)->action(fn ($record) => $record->update(['is_read' => true])),
            \Filament\Tables\Actions\Action::make('reply')->icon('heroicon-o-envelope')->url(fn ($record) => 'mailto:' . $record->email)->openUrlInNewTab(),
        ]);
}

public static function getNavigationBadge(): ?string {
    $count = \App\Models\ContactMessage::unread()->count();
    return $count > 0 ? (string) $count : null;
}

public static function canCreate(): bool { return false; }
```

- [ ] **Step 3: Delete the generated `CreateContactMessage.php` page and remove its route entry**

Edit `app/Filament/Resources/ContactMessageResource.php`'s `getPages()`:
```php
public static function getPages(): array {
    return [
        'index' => \App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages::route('/'),
        'view' => \App\Filament\Resources\ContactMessageResource\Pages\ViewContactMessage::route('/{record}'),
    ];
}
```

Create `app/Filament/Resources/ContactMessageResource/Pages/ViewContactMessage.php`:
```php
<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;
}
```

Delete:
```bash
rm app/Filament/Resources/ContactMessageResource/Pages/CreateContactMessage.php app/Filament/Resources/ContactMessageResource/Pages/EditContactMessage.php
```

- [ ] **Step 4: Add to admin index test**

```php
// add '/admin/contact-messages' to the dataset in AdminResourceIndexTest
```

Run — PASS.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(admin): read-only ContactMessageResource with unread badge"
```

### Task 23: Settings page (Site/Home/About tabs)

**Files:**
- Create: `app/Filament/Pages/Settings.php`
- Create: `resources/views/filament/pages/settings.blade.php`

- [ ] **Step 1: Generate page**

```bash
docker compose exec web php artisan make:filament-page Settings --no-interaction
```

- [ ] **Step 2: Replace `app/Filament/Pages/Settings.php`**

```php
<?php

namespace App\Filament\Pages;

use App\Settings\AboutSettings;
use App\Settings\HomeSettings;
use App\Settings\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 99;

    public array $data = [];

    public function mount(): void {
        $site = app(SiteSettings::class);
        $home = app(HomeSettings::class);
        $about = app(AboutSettings::class);

        $this->form->fill([
            'site' => $site->toArray(),
            'home' => $home->toArray(),
            'about' => $about->toArray(),
        ]);
    }

    public function form(Form $form): Form {
        return $form->schema([
            Tabs::make()->tabs([
                Tabs\Tab::make('Site')->schema([
                    TextInput::make('site.site_title')->required(),
                    TextInput::make('site.tagline')->required(),
                    Textarea::make('site.meta_description')->required(),
                    TextInput::make('site.email')->email()->required(),
                    TextInput::make('site.github_url')->url(),
                    TextInput::make('site.linkedin_url')->url(),
                    TextInput::make('site.twitter_url')->url(),
                    TextInput::make('site.footer_text'),
                    Toggle::make('site.is_available_for_work'),
                ]),
                Tabs\Tab::make('Home')->schema([
                    TextInput::make('home.hero_headline')->required(),
                    Textarea::make('home.hero_subheadline')->required(),
                    TextInput::make('home.hero_cta_text')->required(),
                    TextInput::make('home.hero_cta_url')->required(),
                ]),
                Tabs\Tab::make('About')->schema([
                    RichEditor::make('about.bio')->columnSpanFull(),
                    FileUpload::make('about.profile_image_path')->image()->disk('public')->directory('about')->maxSize(5120),
                    FileUpload::make('about.cv_pdf_path')->acceptedFileTypes(['application/pdf'])->disk('public')->directory('about')->maxSize(10240),
                    TextInput::make('about.location'),
                    TextInput::make('about.years_experience')->numeric(),
                ]),
            ])->columnSpanFull(),
        ])->statePath('data');
    }

    public function save(): void {
        $state = $this->form->getState();

        $site = app(SiteSettings::class);
        foreach ($state['site'] as $k => $v) { $site->{$k} = $v; }
        $site->save();

        $home = app(HomeSettings::class);
        foreach ($state['home'] as $k => $v) { $home->{$k} = $v; }
        $home->save();

        $about = app(AboutSettings::class);
        foreach ($state['about'] as $k => $v) { $about->{$k} = $v; }
        $about->save();

        Notification::make()->title('Settings saved')->success()->send();
    }
}
```

- [ ] **Step 3: Create `resources/views/filament/pages/settings.blade.php`**

```blade
<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit">Save</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
```

- [ ] **Step 4: Feature test**

`tests/Feature/Admin/SettingsPageTest.php`:
```php
<?php

use App\Models\User;

it('loads the settings page for admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin/settings')->assertOk();
});
```

Run — PASS.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(admin): Settings page with Site/Home/About tabs"
```

### Task 24: Dashboard widgets (UnreadMessages, SiteHealth)

**Files:**
- Create: `app/Filament/Widgets/UnreadMessagesWidget.php`
- Create: `app/Filament/Widgets/SiteHealthWidget.php`
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

- [ ] **Step 1: `UnreadMessagesWidget.php`**

```php
<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnreadMessagesWidget extends StatsOverviewWidget
{
    protected function getStats(): array {
        $count = ContactMessage::unread()->count();
        return [
            Stat::make('Unread messages', $count)
                ->description($count > 0 ? 'Needs attention' : 'Inbox clear')
                ->color($count > 0 ? 'warning' : 'success')
                ->url(ContactMessageResource::getUrl('index')),
        ];
    }
}
```

- [ ] **Step 2: `SiteHealthWidget.php`**

```php
<?php

namespace App\Filament\Widgets;

use App\Models\GithubActivityCache;
use App\Settings\SiteSettings;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteHealthWidget extends StatsOverviewWidget
{
    protected function getStats(): array {
        $snap = GithubActivityCache::latestSnapshot();
        $lastSync = $snap?->fetched_at?->diffForHumans() ?? 'never';

        return [
            Stat::make('Available for work', app(SiteSettings::class)->is_available_for_work ? 'Yes' : 'No'),
            Stat::make('Last GitHub sync', $lastSync),
        ];
    }
}
```

- [ ] **Step 3: Register in `AdminPanelProvider.php`**

Inside `panel()` chain add:
```php
->widgets([
    \App\Filament\Widgets\UnreadMessagesWidget::class,
    \App\Filament\Widgets\SiteHealthWidget::class,
])
```

- [ ] **Step 4: Feature test — admin dashboard renders**

`tests/Feature/Admin/DashboardTest.php`:
```php
<?php

use App\Models\User;

it('renders dashboard widgets for admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin')
        ->assertOk()
        ->assertSee('Unread messages')
        ->assertSee('Available for work');
});
```

Run — PASS.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(admin): dashboard widgets for messages + site health"
```

### Task 25: Match Filament primary color to site stub

**Files:**
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

- [ ] **Step 1: Set primary color to neutral**

Inside `panel()` add:
```php
->colors([
    'primary' => \Filament\Support\Colors\Color::Neutral,
])
```

- [ ] **Step 2: Commit**

```bash
git add -A
git commit -m "style(admin): align Filament primary to neutral"
```

---

## Phase 5 — Public pages & partials

### Task 26: Routes + placeholder Livewire page stubs

**Files:**
- Modify: `routes/web.php`
- Create: `app/Livewire/Pages/Home.php` + view
- Create: `app/Livewire/Pages/About.php` + view
- Create: `app/Livewire/Pages/ProjectIndex.php` + view
- Create: `app/Livewire/Pages/ProjectShow.php` + view
- Create: `app/Livewire/Pages/Services.php` + view
- Create: `app/Livewire/Pages/Contact.php` + view

- [ ] **Step 1: Generate each component**

```bash
docker compose exec web php artisan make:livewire Pages/Home
docker compose exec web php artisan make:livewire Pages/About
docker compose exec web php artisan make:livewire Pages/ProjectIndex
docker compose exec web php artisan make:livewire Pages/ProjectShow
docker compose exec web php artisan make:livewire Pages/Services
docker compose exec web php artisan make:livewire Pages/Contact
```

- [ ] **Step 2: Replace `routes/web.php`**

```php
<?php

use App\Livewire\Pages\About;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\ProjectIndex;
use App\Livewire\Pages\ProjectShow;
use App\Livewire\Pages\Services;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/about', About::class)->name('about');
Route::get('/projects', ProjectIndex::class)->name('projects.index');
Route::get('/projects/{slug}', ProjectShow::class)->name('projects.show');
Route::get('/services', Services::class)->name('services');
Route::get('/contact', Contact::class)->name('contact');
```

- [ ] **Step 3: Update each Livewire class to use the shared layout**

In every page class (e.g., `app/Livewire/Pages/Home.php`):
```php
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public function render() {
        return view('livewire.pages.home');
    }
}
```

Do the same for About, ProjectIndex, ProjectShow, Services, Contact.

- [ ] **Step 4: Remove the old placeholder**

```bash
rm resources/views/home-placeholder.blade.php
```

- [ ] **Step 5: Set views to minimal stubs that pass layout test**

Each Blade view (e.g., `resources/views/livewire/pages/home.blade.php`):
```blade
<div>
    <h1 class="text-5xl font-serif mt-12">Home (stub)</h1>
</div>
```
(Adjust the heading text per page: About, Projects, Project detail, Services, Contact.)

- [ ] **Step 6: Update `LayoutTest.php` to match new home content**

Replace the welcome-page assertion:
```php
$response->assertSee('Home (stub)');
```

- [ ] **Step 7: Run full suite**

```bash
docker compose exec web ./vendor/bin/pest
```
Expected: all green.

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat(routes): wire Livewire page stubs for all public routes"
```

### Task 27: Home page — hero + featured projects + CTA

**Files:**
- Modify: `app/Livewire/Pages/Home.php`
- Modify: `resources/views/livewire/pages/home.blade.php`
- Create: `tests/Feature/Pages/HomePageTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Project;
use App\Models\User;

beforeEach(fn () => User::factory()->create(['is_admin' => true]));

it('renders featured projects on home', function () {
    Project::factory()->create(['is_featured' => true, 'is_published' => true, 'title' => 'FeatureOne']);
    Project::factory()->create(['is_featured' => false, 'is_published' => true, 'title' => 'BackgroundTwo']);

    $this->get('/')
        ->assertOk()
        ->assertSee('FeatureOne')
        ->assertDontSee('BackgroundTwo');
});

it('renders the hero headline from settings', function () {
    $this->get('/')->assertSee('Backend engineer who ships.');
});
```

Run — FAIL.

- [ ] **Step 2: `Home.php` component**

```php
<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use App\Settings\HomeSettings;
use App\Settings\SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public function render() {
        return view('livewire.pages.home', [
            'home' => app(HomeSettings::class),
            'site' => app(SiteSettings::class),
            'featuredProjects' => Project::published()->featured()->ordered()->take(4)->get(),
        ]);
    }
}
```

- [ ] **Step 3: `home.blade.php`**

```blade
<div>
    <section class="mt-16 mb-20">
        <p class="text-xs tracking-widest uppercase text-neutral-500 mb-4">{{ $site->tagline }}</p>
        <h1 class="text-5xl md:text-6xl font-serif leading-tight max-w-3xl">{{ $home->hero_headline }}</h1>
        <p class="text-xl text-neutral-600 dark:text-neutral-400 mt-6 max-w-2xl">{{ $home->hero_subheadline }}</p>
        <a href="{{ $home->hero_cta_url }}" wire:navigate class="inline-block mt-8 px-5 py-3 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 rounded hover:opacity-90 transition">{{ $home->hero_cta_text }}</a>
    </section>

    <section class="mb-20">
        <h2 class="text-2xl font-serif mb-8">Selected work</h2>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($featuredProjects as $project)
                <a href="{{ route('projects.show', $project->slug) }}" wire:navigate class="group block p-6 border border-neutral-200 dark:border-neutral-800 rounded-lg hover:-translate-y-1 hover:shadow-lg transition">
                    <h3 class="text-xl font-serif">{{ $project->title }}</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">{{ $project->summary }}</p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach (($project->tech_stack ?? []) as $t)
                            <span class="text-xs font-mono px-2 py-1 rounded bg-neutral-100 dark:bg-neutral-800">{{ $t }}</span>
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @livewire('partials.skills-marquee')
    @livewire('partials.github-strip')

    <section class="my-20">
        <h2 class="text-2xl font-serif mb-4">Want to work together?</h2>
        <a href="/contact" wire:navigate class="underline">Get in touch →</a>
    </section>
</div>
```

- [ ] **Step 4: Run test — expect PASS after we stub the `partials.skills-marquee` and `partials.github-strip` components (Task 32/33). Until then, temporarily stub the blade with `@livewireNothing` by wrapping includes in `@if(false)` — OR run this test after Tasks 32 and 33. For clarity, proceed to Task 32 first.**

Pragmatic: make a minimal stub now so the home test passes.

Create `resources/views/livewire/partials/skills-marquee.blade.php`:
```blade
<div></div>
```
Create `resources/views/livewire/partials/github-strip.blade.php`:
```blade
<div></div>
```
And minimal component classes (from Task 26 generation) already exist. Run tests — PASS.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(home): hero, featured projects, CTA sections"
```

### Task 28: Projects index with reactive tech-stack filter

**Files:**
- Modify: `app/Livewire/Pages/ProjectIndex.php`
- Modify: `resources/views/livewire/pages/project-index.blade.php`
- Create: `tests/Feature/Pages/ProjectIndexTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Project;
use Livewire\Livewire;
use App\Livewire\Pages\ProjectIndex;

it('lists all published projects by default', function () {
    Project::factory()->create(['is_published' => true, 'title' => 'Alpha', 'tech_stack' => ['Laravel']]);
    Project::factory()->create(['is_published' => true, 'title' => 'Beta', 'tech_stack' => ['Redis']]);
    Project::factory()->create(['is_published' => false, 'title' => 'Draft']);

    $this->get('/projects')
        ->assertOk()
        ->assertSee('Alpha')
        ->assertSee('Beta')
        ->assertDontSee('Draft');
});

it('filters by tech stack reactively', function () {
    Project::factory()->create(['is_published' => true, 'title' => 'Alpha', 'tech_stack' => ['Laravel']]);
    Project::factory()->create(['is_published' => true, 'title' => 'Beta', 'tech_stack' => ['Redis']]);

    Livewire::test(ProjectIndex::class)
        ->set('filter', 'Redis')
        ->assertSee('Beta')
        ->assertDontSee('Alpha');
});
```

Run — FAIL.

- [ ] **Step 2: Component**

```php
<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProjectIndex extends Component
{
    #[Url(as: 'tech')]
    public ?string $filter = null;

    public function render() {
        $projects = Project::published()->ordered();

        if ($this->filter) {
            $projects->whereJsonContains('tech_stack', $this->filter);
        }

        $allTags = Project::published()->get()
            ->flatMap(fn ($p) => $p->tech_stack ?? [])
            ->unique()->sort()->values();

        return view('livewire.pages.project-index', [
            'projects' => $projects->get(),
            'allTags' => $allTags,
        ]);
    }
}
```

- [ ] **Step 3: View**

```blade
<div>
    <h1 class="text-4xl font-serif mt-12 mb-2">Projects</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">Case studies from recent work.</p>

    <div class="flex flex-wrap gap-2 mb-8">
        <button wire:click="$set('filter', null)" class="text-xs font-mono px-3 py-1 rounded border {{ $filter === null ? 'bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900' : 'border-neutral-300 dark:border-neutral-700' }}">All</button>
        @foreach ($allTags as $tag)
            <button wire:click="$set('filter', '{{ $tag }}')" class="text-xs font-mono px-3 py-1 rounded border {{ $filter === $tag ? 'bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900' : 'border-neutral-300 dark:border-neutral-700' }}">{{ $tag }}</button>
        @endforeach
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project->slug) }}" wire:navigate class="block p-6 border border-neutral-200 dark:border-neutral-800 rounded-lg hover:-translate-y-1 hover:shadow-lg transition">
                <h3 class="text-xl font-serif">{{ $project->title }}</h3>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">{{ $project->summary }}</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach (($project->tech_stack ?? []) as $t)
                        <span class="text-xs font-mono px-2 py-1 rounded bg-neutral-100 dark:bg-neutral-800">{{ $t }}</span>
                    @endforeach
                </div>
            </a>
        @empty
            <p class="text-neutral-500">No projects match that filter.</p>
        @endforelse
    </div>
</div>
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(projects): reactive tech-stack filter + grid"
```

### Task 29: Project show (case-study) page

**Files:**
- Modify: `app/Livewire/Pages/ProjectShow.php`
- Modify: `resources/views/livewire/pages/project-show.blade.php`
- Create: `tests/Feature/Pages/ProjectShowTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Project;

it('renders a published project by slug', function () {
    $p = Project::factory()->create([
        'is_published' => true,
        'slug' => 'billing-v2',
        'title' => 'Billing V2',
        'problem' => '<p>Old billing was slow.</p>',
    ]);

    $this->get("/projects/{$p->slug}")
        ->assertOk()
        ->assertSee('Billing V2')
        ->assertSee('Old billing was slow.', false);
});

it('returns 404 for unpublished projects', function () {
    Project::factory()->create(['is_published' => false, 'slug' => 'hidden']);
    $this->get('/projects/hidden')->assertNotFound();
});
```

Run — FAIL.

- [ ] **Step 2: Component**

```php
<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProjectShow extends Component
{
    public Project $project;

    public function mount(string $slug) {
        $this->project = Project::published()->where('slug', $slug)->firstOrFail();
    }

    public function render() {
        $previous = Project::published()->ordered()
            ->where('sort_order', '<', $this->project->sort_order)->orderByDesc('sort_order')->first();
        $next = Project::published()->ordered()
            ->where('sort_order', '>', $this->project->sort_order)->first();

        return view('livewire.pages.project-show', compact('previous', 'next'));
    }
}
```

- [ ] **Step 3: View**

```blade
<div>
    <article class="prose dark:prose-invert max-w-none mt-10">
        <header class="not-prose mb-10">
            <p class="text-xs font-mono uppercase text-neutral-500 tracking-widest">{{ $project->role ?? 'Case study' }}</p>
            <h1 class="text-5xl font-serif mt-2">{{ $project->title }}</h1>
            <p class="text-xl text-neutral-600 dark:text-neutral-400 mt-4">{{ $project->summary }}</p>
            <div class="flex flex-wrap gap-2 mt-6">
                @foreach (($project->tech_stack ?? []) as $t)
                    <span class="text-xs font-mono px-2 py-1 rounded bg-neutral-100 dark:bg-neutral-800">{{ $t }}</span>
                @endforeach
            </div>
            @if ($project->cover_image_path)
                <img src="{{ asset('storage/' . $project->cover_image_path) }}" alt="" class="mt-8 rounded-lg w-full">
            @endif
        </header>

        @if ($project->problem)
            <h2>Problem</h2>
            {!! $project->problem !!}
        @endif

        @if ($project->approach)
            <h2>Approach</h2>
            {!! $project->approach !!}
            @if ($project->architecture_image_path)
                <img src="{{ asset('storage/' . $project->architecture_image_path) }}" alt="" class="rounded-lg w-full">
            @endif
        @endif

        @if ($project->challenges)
            <h2>Key challenges</h2>
            {!! $project->challenges !!}
        @endif

        @if ($project->outcome)
            <h2>Outcome</h2>
            {!! $project->outcome !!}
        @endif

        <p class="not-prose flex gap-4 mt-10">
            @if ($project->repo_url) <a href="{{ $project->repo_url }}" class="underline">Code →</a> @endif
            @if ($project->demo_url) <a href="{{ $project->demo_url }}" class="underline">Live demo →</a> @endif
        </p>
    </article>

    <nav class="flex justify-between border-t border-neutral-200 dark:border-neutral-800 mt-16 pt-6 text-sm">
        <div>@if ($previous) <a href="{{ route('projects.show', $previous->slug) }}" wire:navigate>← {{ $previous->title }}</a> @endif</div>
        <div>@if ($next) <a href="{{ route('projects.show', $next->slug) }}" wire:navigate>{{ $next->title }} →</a> @endif</div>
    </nav>
</div>
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(projects): case-study page with adjacent navigation"
```

### Task 30: About page

**Files:**
- Modify: `app/Livewire/Pages/About.php` + view
- Create: `tests/Feature/Pages/AboutPageTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Experience;

it('renders experiences on about page', function () {
    Experience::factory()->create(['company' => 'AcmeCo']);
    $this->get('/about')->assertOk()->assertSee('AcmeCo');
});
```

Run — FAIL.

- [ ] **Step 2: Component**

```php
<?php

namespace App\Livewire\Pages;

use App\Models\Experience;
use App\Settings\AboutSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class About extends Component
{
    public function render() {
        return view('livewire.pages.about', [
            'about' => app(AboutSettings::class),
            'experiences' => Experience::ordered()->get(),
        ]);
    }
}
```

- [ ] **Step 3: View**

```blade
<div>
    <section class="mt-12 grid md:grid-cols-3 gap-10">
        @if ($about->profile_image_path)
            <img src="{{ asset('storage/' . $about->profile_image_path) }}" alt="" class="rounded-lg w-full max-w-xs">
        @endif
        <div class="md:col-span-2">
            <h1 class="text-4xl font-serif mb-6">About</h1>
            <div class="prose dark:prose-invert max-w-none">{!! $about->bio !!}</div>
            @if ($about->cv_pdf_path)
                <a href="{{ asset('storage/' . $about->cv_pdf_path) }}" class="inline-block mt-6 underline">Download CV (PDF) →</a>
            @endif
        </div>
    </section>

    <section class="mt-20">
        <h2 class="text-2xl font-serif mb-8">Experience</h2>
        <ol class="space-y-8 border-l border-neutral-200 dark:border-neutral-800 pl-6">
            @foreach ($experiences as $e)
                <li>
                    <div class="text-xs font-mono uppercase text-neutral-500">{{ $e->started_at?->format('Y') }} — {{ $e->ended_at?->format('Y') ?? 'Present' }}</div>
                    <h3 class="text-lg font-serif mt-1">{{ $e->role }}, {{ $e->company }}</h3>
                    @if ($e->description) <div class="prose dark:prose-invert max-w-none mt-2">{!! $e->description !!}</div> @endif
                </li>
            @endforeach
        </ol>
    </section>
</div>
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(about): bio, profile image, experience timeline"
```

### Task 31: Services page

**Files:**
- Modify: `app/Livewire/Pages/Services.php` + view
- Create: `tests/Feature/Pages/ServicesPageTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

use App\Models\Service;

it('lists only published services', function () {
    Service::factory()->create(['is_published' => true, 'title' => 'API design']);
    Service::factory()->create(['is_published' => false, 'title' => 'Hidden']);
    $this->get('/services')->assertOk()->assertSee('API design')->assertDontSee('Hidden');
});
```

Run — FAIL.

- [ ] **Step 2: Component**

```php
<?php

namespace App\Livewire\Pages;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Services extends Component
{
    public function render() {
        return view('livewire.pages.services', [
            'services' => Service::published()->ordered()->get(),
        ]);
    }
}
```

- [ ] **Step 3: View**

```blade
<div>
    <h1 class="text-4xl font-serif mt-12 mb-10">Services</h1>
    <div class="grid gap-8 md:grid-cols-2">
        @foreach ($services as $service)
            <div class="p-6 border border-neutral-200 dark:border-neutral-800 rounded-lg">
                <h2 class="text-xl font-serif">{{ $service->title }}</h2>
                @if ($service->starting_price) <p class="text-xs font-mono text-neutral-500 mt-1">{{ $service->starting_price }}</p> @endif
                <div class="prose dark:prose-invert max-w-none mt-4 text-sm">{!! $service->description !!}</div>
            </div>
        @endforeach
    </div>
    <div class="mt-12">
        <a href="/contact" wire:navigate class="inline-block px-5 py-3 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 rounded hover:opacity-90 transition">Start a conversation</a>
    </div>
</div>
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(services): public services page"
```

### Task 32: Contact form (honeypot, rate limit, queued mail)

**Files:**
- Modify: `app/Livewire/Pages/Contact.php` + view
- Create: `app/Mail/ContactNotification.php`
- Create: `tests/Feature/Pages/ContactFormTest.php`

- [ ] **Step 1: Failing tests**

```php
<?php

use App\Livewire\Pages\Contact;
use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    RateLimiter::clear('contact:' . request()->ip());
});

it('validates required fields', function () {
    Livewire::test(Contact::class)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'message']);
});

it('stores a valid submission and sends mail', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Alice')
        ->set('email', 'a@b.com')
        ->set('subject', 'Hi')
        ->set('message', 'Hello there')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(1);
    Mail::assertQueued(ContactNotification::class);
});

it('rejects bots via honeypot', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Alice')
        ->set('email', 'a@b.com')
        ->set('subject', 'Hi')
        ->set('message', 'Hello')
        ->set('website', 'spam.example')   // honeypot field
        ->call('submit')
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingQueued();
});

it('rate limits a second submission within 60s', function () {
    $first = Livewire::test(Contact::class)
        ->set('name', 'Alice')->set('email', 'a@b.com')
        ->set('subject', 'Hi')->set('message', 'msg1')
        ->call('submit');

    $second = Livewire::test(Contact::class)
        ->set('name', 'Alice')->set('email', 'a@b.com')
        ->set('subject', 'Hi')->set('message', 'msg2')
        ->call('submit')
        ->assertHasErrors(['message']);

    expect(ContactMessage::count())->toBe(1);
});
```

Run — FAIL.

- [ ] **Step 2: Create `app/Mail/ContactNotification.php`**

```bash
docker compose exec web php artisan make:mail ContactNotification --markdown=mail.contact-notification
```

Replace `app/Mail/ContactNotification.php`:
```php
<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope {
        return new Envelope(subject: 'New contact: ' . $this->contact->subject);
    }

    public function content(): Content {
        return new Content(markdown: 'mail.contact-notification', with: ['contact' => $this->contact]);
    }
}
```

`resources/views/mail/contact-notification.blade.php`:
```blade
<x-mail::message>
# New contact message

**From:** {{ $contact->name }} <{{ $contact->email }}>
**Subject:** {{ $contact->subject }}

{{ $contact->message }}

— Portfolio
</x-mail::message>
```

- [ ] **Step 3: `Contact.php` component**

```php
<?php

namespace App\Livewire\Pages;

use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Settings\SiteSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Contact extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('required|string|max:150')]
    public string $subject = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    public string $website = ''; // honeypot

    public bool $sent = false;

    public function submit() {
        if ($this->website !== '') {
            // silently accept — do nothing, pretend success
            $this->sent = true;
            return;
        }

        $this->validate();

        $ip = request()->ip();
        $key = 'contact:' . $ip;
        if (! RateLimiter::attempt($key, 1, fn () => null, 60)) {
            $this->addError('message', 'Too many submissions. Please wait a minute.');
            return;
        }

        $row = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
        ]);

        Mail::to(app(SiteSettings::class)->email)->queue(new ContactNotification($row));

        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->sent = true;
    }

    public function render() { return view('livewire.pages.contact'); }
}
```

- [ ] **Step 4: View**

```blade
<div>
    <h1 class="text-4xl font-serif mt-12 mb-4">Contact</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-10 max-w-2xl">Let me know what you're building.</p>

    @if ($sent)
        <div class="p-4 border border-green-300 dark:border-green-700 rounded bg-green-50 dark:bg-green-900/30 text-sm">
            Thanks — I'll reply shortly.
        </div>
    @else
        <form wire:submit="submit" class="max-w-xl space-y-4">
            <div class="absolute left-[-9999px]" aria-hidden="true">
                <label>Website <input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div>
                <label class="block text-sm mb-1">Name</label>
                <input type="text" wire:model="name" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded bg-transparent">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded bg-transparent">
                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Subject</label>
                <input type="text" wire:model="subject" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded bg-transparent">
                @error('subject') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Message</label>
                <textarea wire:model="message" rows="6" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded bg-transparent"></textarea>
                @error('message') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-3 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 rounded hover:opacity-90 transition">Send</button>
        </form>
    @endif
</div>
```

- [ ] **Step 5: Run — PASS**

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(contact): livewire form with honeypot, rate limit, queued mail"
```

### Task 33: Skills marquee partial

**Files:**
- Modify: `app/Livewire/Partials/SkillsMarquee.php`
- Modify: `resources/views/livewire/partials/skills-marquee.blade.php`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Component**

```php
<?php

namespace App\Livewire\Partials;

use App\Models\Skill;
use Livewire\Component;

class SkillsMarquee extends Component
{
    public function render() {
        return view('livewire.partials.skills-marquee', [
            'skills' => Skill::ordered()->get(),
        ]);
    }
}
```

- [ ] **Step 2: View**

```blade
<section class="my-20 overflow-hidden" aria-label="Skills">
    <h2 class="sr-only">Tech stack</h2>
    <div class="marquee">
        <div class="marquee-track">
            @foreach ($skills as $s)
                <span class="marquee-item">{{ $s->name }}</span>
            @endforeach
            @foreach ($skills as $s)
                <span class="marquee-item" aria-hidden="true">{{ $s->name }}</span>
            @endforeach
        </div>
    </div>
</section>
```

- [ ] **Step 3: Append marquee CSS to `resources/css/app.css`**

```css
.marquee { @apply relative w-full; }
.marquee-track { @apply flex gap-10 animate-[marquee_40s_linear_infinite] whitespace-nowrap; }
.marquee:hover .marquee-track { animation-play-state: paused; }
.marquee-item { @apply font-mono text-sm text-neutral-500; }

@keyframes marquee {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}
```

- [ ] **Step 4: Rebuild assets**

```bash
docker compose exec web npm run build
```

- [ ] **Step 5: Test**

Add to `HomePageTest.php`:
```php
it('renders skill names in the marquee', function () {
    \App\Models\Skill::factory()->create(['name' => 'Laravel']);
    $this->get('/')->assertSee('Laravel');
});
```

Run — PASS.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat(partials): skills marquee with pause-on-hover"
```

### Task 34: GitHub strip partial (reads cache, hides if empty)

**Files:**
- Modify: `app/Livewire/Partials/GithubStrip.php`
- Modify: `resources/views/livewire/partials/github-strip.blade.php`

- [ ] **Step 1: Component**

```php
<?php

namespace App\Livewire\Partials;

use App\Models\GithubActivityCache;
use Livewire\Component;

class GithubStrip extends Component
{
    public function render() {
        return view('livewire.partials.github-strip', [
            'snapshot' => GithubActivityCache::latestSnapshot(),
        ]);
    }
}
```

- [ ] **Step 2: View**

```blade
<section>
    @if ($snapshot && ! empty($snapshot->payload['events'] ?? []))
        <div class="my-16">
            <h2 class="text-sm font-mono uppercase text-neutral-500 tracking-widest mb-4">Recent activity</h2>
            <ul class="grid gap-2 text-sm">
                @foreach (array_slice($snapshot->payload['events'], 0, 5) as $event)
                    <li class="text-neutral-600 dark:text-neutral-400">
                        <span class="font-mono text-xs">{{ $event['type'] ?? 'event' }}</span>
                        {{ $event['repo'] ?? '' }}
                        <span class="text-xs text-neutral-500">{{ $event['at'] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
```

- [ ] **Step 3: Test**

Add to `tests/Feature/Pages/HomePageTest.php`:
```php
it('hides github strip when cache is empty', function () {
    $this->get('/')->assertDontSee('Recent activity');
});

it('shows github strip when cache has events', function () {
    \App\Models\GithubActivityCache::create([
        'payload' => ['events' => [['type' => 'PushEvent', 'repo' => 'acme/portfolio', 'at' => '2026-04-12']]],
        'fetched_at' => now(),
    ]);
    $this->get('/')->assertSee('Recent activity')->assertSee('acme/portfolio');
});
```

Run — PASS.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat(partials): github activity strip reads single-row cache"
```

---

## Phase 6 — Background jobs & scheduling

### Task 35: `FetchGithubActivity` job

**Files:**
- Create: `app/Jobs/FetchGithubActivity.php`
- Modify: `bootstrap/app.php` (schedule block) OR uncomment `scheduler` in `docker-compose.yml`
- Create: `tests/Unit/Jobs/FetchGithubActivityTest.php`

- [ ] **Step 1: Failing tests**

```php
<?php

use App\Jobs\FetchGithubActivity;
use App\Models\GithubActivityCache;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => config(['services.github.token' => 'fake-token', 'services.github.username' => 'testuser']));

it('writes a fresh snapshot on success', function () {
    Http::fake([
        'api.github.com/*' => Http::response([
            ['type' => 'PushEvent', 'repo' => ['name' => 'acme/test'], 'created_at' => '2026-04-01T00:00:00Z'],
        ], 200),
    ]);

    (new FetchGithubActivity)->handle();

    $snap = GithubActivityCache::latestSnapshot();
    expect($snap)->not->toBeNull()
        ->and($snap->payload['events'])->toHaveCount(1)
        ->and($snap->payload['events'][0]['type'])->toBe('PushEvent');
});

it('does not overwrite cache on API failure', function () {
    GithubActivityCache::create(['payload' => ['events' => [['type' => 'old']]], 'fetched_at' => now()->subHour()]);
    Http::fake(['api.github.com/*' => Http::response('nope', 500)]);

    (new FetchGithubActivity)->handle();

    expect(GithubActivityCache::latestSnapshot()->payload['events'][0]['type'])->toBe('old');
});

it('tolerates empty cache on first run with failure', function () {
    Http::fake(['api.github.com/*' => Http::response('err', 500)]);

    (new FetchGithubActivity)->handle();

    expect(GithubActivityCache::count())->toBe(0);
});
```

Run — FAIL (job not defined, config keys missing).

- [ ] **Step 2: Add service config — `config/services.php`**

Append to the returned array:
```php
'github' => [
    'token' => env('GITHUB_TOKEN'),
    'username' => env('GITHUB_USERNAME', 'octocat'),
],
```

Append to `.env`:
```
GITHUB_TOKEN=
GITHUB_USERNAME=octocat
```

- [ ] **Step 3: Create the job**

```bash
docker compose exec web php artisan make:job FetchGithubActivity
```

Replace with:
```php
<?php

namespace App\Jobs;

use App\Models\GithubActivityCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGithubActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $user = config('services.github.username');
        $token = config('services.github.token');

        $request = Http::withHeaders(['Accept' => 'application/vnd.github+json']);
        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/users/{$user}/events/public");

        if ($response->failed()) {
            Log::warning('FetchGithubActivity: API failed', ['status' => $response->status()]);
            return;
        }

        $events = collect($response->json())
            ->take(10)
            ->map(fn ($e) => [
                'type' => $e['type'] ?? 'event',
                'repo' => $e['repo']['name'] ?? null,
                'at' => $e['created_at'] ?? null,
            ])
            ->values()
            ->all();

        GithubActivityCache::create([
            'payload' => ['events' => $events],
            'fetched_at' => now(),
        ]);
    }
}
```

- [ ] **Step 4: Schedule it hourly — `bootstrap/app.php`**

Add after `->withMiddleware(...)`:
```php
->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->job(new \App\Jobs\FetchGithubActivity)->hourly();
})
```

- [ ] **Step 5: Uncomment the `scheduler` service in `docker-compose.yml`**

Strip the leading `# ` from lines 66–82 (the scheduler service block). Ensure:
```yaml
scheduler:
    build: ./
    container_name: ${CONTAINER_LABEL}_scheduler
    working_dir: /app
    volumes:
        - .:/app
        - ./storage:/app/storage
    environment:
        - PHP_DATE_TIMEZONE="Asia/Manila"
    entrypoint: ["/usr/local/bin/php"]
    command: ["artisan", "schedule:work"]
    networks:
        - internal
    depends_on:
        mysql:
            condition: service_healthy
    restart: unless-stopped
```

- [ ] **Step 6: Bring up scheduler, run — PASS**

```bash
docker compose up -d scheduler
docker compose exec web ./vendor/bin/pest tests/Unit/Jobs/FetchGithubActivityTest.php
```

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat(jobs): hourly GitHub activity fetch with stale-safe cache"
```

### Task 36: Queue worker sanity + mail flow

**Files:**
- No code changes; runtime verification.

- [ ] **Step 1: Restart queue worker**

```bash
docker compose restart queue
```

- [ ] **Step 2: Submit a live contact form (browser) → check Mailpit UI**

In a browser: http://localhost:8080/contact → submit → open http://localhost:8025 → see the queued email.

- [ ] **Step 3: Commit (documentation-only if anything changes)**

```bash
git status
```
If nothing changed, skip. Otherwise commit.

---

## Phase 7 — Error pages & final polish

### Task 37: Custom 404 + 500 pages

**Files:**
- Create: `resources/views/errors/404.blade.php`
- Create: `resources/views/errors/500.blade.php`
- Create: `tests/Feature/ErrorPagesTest.php`

- [ ] **Step 1: Failing test**

```php
<?php

it('shows custom 404 page with styled layout', function () {
    $this->get('/this-route-does-not-exist')
        ->assertNotFound()
        ->assertSee('Page not found', false);
});
```

Run — FAIL.

- [ ] **Step 2: `resources/views/errors/404.blade.php`**

```blade
<x-layouts.app>
    <section class="mt-24 text-center">
        <p class="text-xs font-mono uppercase tracking-widest text-neutral-500">404</p>
        <h1 class="text-5xl font-serif mt-4">Page not found</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-4">That URL isn't here. Try one of these:</p>
        <div class="flex justify-center gap-6 mt-8 text-sm">
            <a href="/" wire:navigate class="underline">Home</a>
            <a href="/projects" wire:navigate class="underline">Projects</a>
            <a href="/contact" wire:navigate class="underline">Contact</a>
        </div>
    </section>
</x-layouts.app>
```

- [ ] **Step 3: `resources/views/errors/500.blade.php`**

```blade
<x-layouts.app>
    <section class="mt-24 text-center">
        <p class="text-xs font-mono uppercase tracking-widest text-neutral-500">500</p>
        <h1 class="text-5xl font-serif mt-4">Something broke</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-4">This one's on me. Try again shortly.</p>
        <a href="/" wire:navigate class="inline-block mt-8 underline text-sm">Back home</a>
    </section>
</x-layouts.app>
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat(errors): custom 404 and 500 pages matching site aesthetic"
```

### Task 38: Full-suite green + seed + manual smoke

**Files:**
- No code changes; verification.

- [ ] **Step 1: Run the whole suite**

```bash
docker compose exec web ./vendor/bin/pest
```
Expected: all green.

- [ ] **Step 2: Fresh seed**

```bash
docker compose exec web php artisan migrate:fresh --seed
```

- [ ] **Step 3: Manual smoke in browser**

Visit each in turn:
- http://localhost:8080/ — hero, featured projects render
- http://localhost:8080/about — bio + experiences
- http://localhost:8080/projects — filter buttons work
- http://localhost:8080/projects/<any-slug> — case study renders
- http://localhost:8080/services — services listed
- http://localhost:8080/contact — form validates, submission shows success + email lands in Mailpit
- http://localhost:8080/admin/login — login as `admin@example.com` / `password`
- http://localhost:8080/admin — dashboard widgets render; each resource loads; Settings page saves

- [ ] **Step 4: Tag v1**

```bash
git tag v1.0.0-rc1
git log --oneline -20
```

- [ ] **Step 5: Final commit (if any last tweaks)**

```bash
git status
git add -A
git diff --cached --quiet || git commit -m "chore: v1 ready for manual QA"
```

---

## Self-review checklist performed

- **Spec coverage:**
    - §1 Purpose — covered by the entire plan.
    - §2 Audience & tone — no code impact; visual tone baked into layout and typography (Tasks 7, 8, 27).
    - §3 Visual direction — Tasks 7 (Tailwind + fonts), 8 (layout), 25 (Filament accent).
    - §4 Interactivity — Tasks 8 (`wire:navigate`, theme toggle), 33 (marquee); hero word-by-word fade is intentionally **deferred as optional** — see note below.
    - §5 Site map & routes — Task 26.
    - §6 Case-study structure — Task 29.
    - §7 Data model — Tasks 11–18.
    - §8 Filament resources — Tasks 20–24.
    - §9 Livewire components — Tasks 26–34.
    - §10 Background jobs — Tasks 35–36.
    - §11 Styling & theming — Tasks 7, 25.
    - §12 Error handling & validation — Tasks 32 (contact), 37 (error pages).
    - §13 Security — Task 19 (admin gate), 20 (upload limits), 32 (honeypot + rate limit).
    - §14 Testing — per-task tests; full suite in Task 38.
    - §15 Deployment — local-only is already true; `.env` Redis switch in Task 5.
    - §16 Directory layout — matches.
    - §17 Deferred items — respected (no blog, no analytics, no accent color pick, no sitemap).

- **Spec gap noted — hero word-by-word fade-in:** The spec names it as a signature moment. Not explicitly included as a task to keep plan size down. **Add a small CSS keyframe step inside Task 27 (home page) if you want it from day one; otherwise it's trivial to add later.**

- **Placeholder scan:** no "TBD" / "add error handling" / "similar to Task N" references remain. Each step has concrete code or exact commands.

- **Type consistency:** Model method names (`published`, `featured`, `ordered`, `unread`, `latestSnapshot`) are consistent between tasks. Settings class names are consistent (`SiteSettings`, `HomeSettings`, `AboutSettings`). Route names (`home`, `about`, `projects.index`, `projects.show`, `services`, `contact`) reused consistently.

---
