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
