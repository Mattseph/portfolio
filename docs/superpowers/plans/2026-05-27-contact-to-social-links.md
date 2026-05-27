# Contact-to-Social-Links Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the contact form at `/contact` with icon+label social cards (GitHub, LinkedIn, Twitter) and add social icons to the site footer, deleting all contact-form backend code.

**Architecture:** Slim the existing `Contact` Livewire component to just inject `SiteSettings` and pass the three nullable social URLs to the view. The contact page view is rewritten as static social cards. The shared layout footer is updated to show small social icons. All contact form backend code (model, factory, migration, mail, Filament resource, tests) is deleted.

**Tech Stack:** Laravel 11, Livewire 3, Tailwind 3, spatie/laravel-settings, Pest 2

---

## File Map

| Action | Path |
|--------|------|
| Delete | `app/Models/ContactMessage.php` |
| Delete | `app/Mail/ContactNotification.php` |
| Delete | `app/Filament/Resources/ContactMessageResource.php` |
| Delete | `app/Filament/Resources/ContactMessageResource/Pages/ListContactMessages.php` |
| Delete | `app/Filament/Resources/ContactMessageResource/Pages/ViewContactMessage.php` |
| Delete | `resources/views/mail/contact-notification.blade.php` |
| Delete | `database/factories/ContactMessageFactory.php` |
| Delete | `database/migrations/2026_04_14_013937_create_contact_messages_table.php` |
| Delete | `tests/Feature/Pages/ContactFormTest.php` |
| Delete | `tests/Feature/ContactMessageModelTest.php` |
| Create | `tests/Feature/Pages/ContactPageTest.php` |
| Modify | `app/Livewire/Pages/Contact.php` |
| Modify | `resources/views/livewire/pages/contact.blade.php` |
| Modify | `resources/views/components/layouts/app.blade.php` |

---

### Task 1: Delete contact form backend code

**Files:**
- Delete: `app/Models/ContactMessage.php`
- Delete: `app/Mail/ContactNotification.php`
- Delete: `app/Filament/Resources/ContactMessageResource.php`
- Delete: `app/Filament/Resources/ContactMessageResource/Pages/ListContactMessages.php`
- Delete: `app/Filament/Resources/ContactMessageResource/Pages/ViewContactMessage.php`
- Delete: `resources/views/mail/contact-notification.blade.php`
- Delete: `database/factories/ContactMessageFactory.php`
- Delete: `database/migrations/2026_04_14_013937_create_contact_messages_table.php`
- Delete: `tests/Feature/Pages/ContactFormTest.php`
- Delete: `tests/Feature/ContactMessageModelTest.php`

- [ ] **Step 1: Delete all contact form backend and test files**

```bash
rm app/Models/ContactMessage.php
rm app/Mail/ContactNotification.php
rm app/Filament/Resources/ContactMessageResource.php
rm -rf app/Filament/Resources/ContactMessageResource/
rm resources/views/mail/contact-notification.blade.php
rm database/factories/ContactMessageFactory.php
rm database/migrations/2026_04_14_013937_create_contact_messages_table.php
rm tests/Feature/Pages/ContactFormTest.php
rm tests/Feature/ContactMessageModelTest.php
```

- [ ] **Step 2: Verify the files are gone**

```bash
ls app/Models/ContactMessage.php 2>&1 || echo "deleted"
ls tests/Feature/Pages/ContactFormTest.php 2>&1 || echo "deleted"
```

Expected: both print "deleted"

---

### Task 2: Write the failing test for the new contact page

**Files:**
- Create: `tests/Feature/Pages/ContactPageTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Pages/ContactPageTest.php`:

```php
<?php

it('renders the contact page', function () {
    $this->get('/contact')->assertOk()->assertSee('Get in touch');
});

it('shows github link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->github_url = 'https://github.com/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://github.com/testuser');
});

it('shows linkedin link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->linkedin_url = 'https://linkedin.com/in/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://linkedin.com/in/testuser');
});

it('shows twitter link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->twitter_url = 'https://twitter.com/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://twitter.com/testuser');
});
```

- [ ] **Step 2: Run the tests to confirm they fail**

Run inside the Docker container (`make ssh` first):

```bash
./vendor/bin/pest tests/Feature/Pages/ContactPageTest.php -v
```

Expected: first test FAILS (view still has the old form content), or passes if the route still loads. The key is the `assertSee('Get in touch')` check — it will fail because the view still says "Contact".

---

### Task 3: Slim the Contact Livewire component

**Files:**
- Modify: `app/Livewire/Pages/Contact.php`

- [ ] **Step 1: Replace Contact.php with the slimmed version**

Replace the full contents of `app/Livewire/Pages/Contact.php` with:

```php
<?php

namespace App\Livewire\Pages;

use App\Settings\SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Contact extends Component
{
    public function render()
    {
        $site = app(SiteSettings::class);

        return view('livewire.pages.contact', [
            'github'   => $site->github_url,
            'linkedin' => $site->linkedin_url,
            'twitter'  => $site->twitter_url,
        ]);
    }
}
```

---

### Task 4: Replace the contact page view with social cards

**Files:**
- Modify: `resources/views/livewire/pages/contact.blade.php`

- [ ] **Step 1: Replace the view with social cards**

Replace the full contents of `resources/views/livewire/pages/contact.blade.php` with:

```blade
<div>
    <h1 class="text-4xl font-serif mt-12 mb-4">Get in touch</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-10 max-w-2xl">Find me on the web.</p>

    <div class="space-y-4 max-w-sm">
        @if ($github)
        <a href="{{ $github }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-4 p-4 border border-neutral-200 dark:border-neutral-700 rounded-lg hover:border-accent dark:hover:border-accent-dark transition-colors group">
            <svg class="w-6 h-6 shrink-0 text-neutral-700 dark:text-neutral-300 group-hover:text-accent dark:group-hover:text-accent-dark transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
            </svg>
            <div class="min-w-0">
                <div class="font-medium">GitHub</div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400 truncate">{{ $github }}</div>
            </div>
        </a>
        @endif

        @if ($linkedin)
        <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-4 p-4 border border-neutral-200 dark:border-neutral-700 rounded-lg hover:border-accent dark:hover:border-accent-dark transition-colors group">
            <svg class="w-6 h-6 shrink-0 text-neutral-700 dark:text-neutral-300 group-hover:text-accent dark:group-hover:text-accent-dark transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
            <div class="min-w-0">
                <div class="font-medium">LinkedIn</div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400 truncate">{{ $linkedin }}</div>
            </div>
        </a>
        @endif

        @if ($twitter)
        <a href="{{ $twitter }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-4 p-4 border border-neutral-200 dark:border-neutral-700 rounded-lg hover:border-accent dark:hover:border-accent-dark transition-colors group">
            <svg class="w-6 h-6 shrink-0 text-neutral-700 dark:text-neutral-300 group-hover:text-accent dark:group-hover:text-accent-dark transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <div class="min-w-0">
                <div class="font-medium">Twitter / X</div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400 truncate">{{ $twitter }}</div>
            </div>
        </a>
        @endif

        @if (!$github && !$linkedin && !$twitter)
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">No social links configured yet.</p>
        @endif
    </div>
</div>
```

- [ ] **Step 2: Run the contact page tests — all should pass now**

```bash
./vendor/bin/pest tests/Feature/Pages/ContactPageTest.php -v
```

Expected: all 4 tests PASS.

---

### Task 5: Add social icons to the footer

**Files:**
- Modify: `resources/views/components/layouts/app.blade.php`

- [ ] **Step 1: Replace the footer section**

In `resources/views/components/layouts/app.blade.php`, replace the `<footer>` block (lines 44–49):

```blade
    <footer class="max-w-5xl mx-auto px-6 py-8 text-xs text-neutral-500 border-t border-neutral-200 dark:border-neutral-800">
        <div class="flex justify-between items-center">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
            <span>Built with Laravel</span>
        </div>
    </footer>
```

With:

```blade
    <footer class="max-w-5xl mx-auto px-6 py-8 text-xs text-neutral-500 border-t border-neutral-200 dark:border-neutral-800">
        <div class="flex justify-between items-center gap-4">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
            <div class="flex items-center gap-3">
                @php $site = app(\App\Settings\SiteSettings::class); @endphp
                @if($site->github_url)
                <a href="{{ $site->github_url }}" target="_blank" rel="noopener noreferrer"
                   class="hover:text-neutral-800 dark:hover:text-neutral-200 transition-colors" aria-label="GitHub">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                    </svg>
                </a>
                @endif
                @if($site->linkedin_url)
                <a href="{{ $site->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                   class="hover:text-neutral-800 dark:hover:text-neutral-200 transition-colors" aria-label="LinkedIn">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                @endif
                @if($site->twitter_url)
                <a href="{{ $site->twitter_url }}" target="_blank" rel="noopener noreferrer"
                   class="hover:text-neutral-800 dark:hover:text-neutral-200 transition-colors" aria-label="Twitter / X">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                @endif
                <span>Built with Laravel</span>
            </div>
        </div>
    </footer>
```

- [ ] **Step 2: Run the full test suite to check for regressions**

```bash
./vendor/bin/pest --v
```

Expected: all tests pass. The removed `ContactFormTest` and `ContactMessageModelTest` are gone, so no orphan references.

---

### Task 6: Final commit

- [ ] **Step 1: Commit**

```bash
git add -A
git commit -m "feat(contact): replace form with social links, add icons to footer"
```

- [ ] **Step 2: Run make fresh to clean the orphaned contact_messages table from the dev database**

On the host machine:

```bash
make fresh
```

This drops and recreates the database from current migrations (the contact_messages migration is deleted, so the table won't be recreated).
