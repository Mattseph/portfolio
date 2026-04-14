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
