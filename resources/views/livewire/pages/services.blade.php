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
        <a href="/contact" wire:navigate class="inline-block px-5 py-3 bg-accent text-white dark:bg-accent-dark dark:text-neutral-950 rounded hover:opacity-90 transition">Start a conversation</a>
    </div>
</div>
