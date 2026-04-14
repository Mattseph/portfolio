<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ dark: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('dark', v => { localStorage.theme = v ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (function () {
            var stored = localStorage.theme;
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
        document.addEventListener('livewire:navigated', function () {
            var stored = localStorage.theme;
            if (stored === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <header class="max-w-5xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="/" wire:navigate class="font-serif text-xl font-semibold tracking-tight hover:text-accent dark:hover:text-accent-dark transition-colors">{{ config('app.name') }}</a>
        <nav class="flex items-center gap-6 text-sm">
            <a href="/" wire:navigate class="hover:text-accent dark:hover:text-accent-dark transition-colors">Home</a>
            <a href="/about" wire:navigate class="hover:text-accent dark:hover:text-accent-dark transition-colors">About</a>
            <a href="/projects" wire:navigate class="hover:text-accent dark:hover:text-accent-dark transition-colors">Projects</a>
            <a href="/services" wire:navigate class="hover:text-accent dark:hover:text-accent-dark transition-colors">Services</a>
            <a href="/contact" wire:navigate class="hover:text-accent dark:hover:text-accent-dark transition-colors">Contact</a>
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
