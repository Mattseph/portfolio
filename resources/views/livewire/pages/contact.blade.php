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
            <button type="submit" class="px-5 py-3 bg-accent text-white dark:bg-accent-dark dark:text-neutral-950 rounded hover:opacity-90 transition">Send</button>
        </form>
    @endif
</div>
