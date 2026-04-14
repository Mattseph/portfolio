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
