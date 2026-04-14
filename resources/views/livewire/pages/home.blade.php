<div>
    <section class="mt-16 mb-20">
        <p class="text-xs tracking-widest uppercase text-accent dark:text-accent-dark mb-4">{{ $site->tagline }}</p>
        <h1 class="text-5xl md:text-6xl font-serif leading-tight max-w-3xl">{{ $home->hero_headline }}</h1>
        <p class="text-xl text-neutral-600 dark:text-neutral-400 mt-6 max-w-2xl">{{ $home->hero_subheadline }}</p>
        <a href="{{ $home->hero_cta_url }}" wire:navigate class="inline-block mt-8 px-5 py-3 bg-accent text-white dark:bg-accent-dark dark:text-neutral-950 rounded hover:opacity-90 transition">{{ $home->hero_cta_text }}</a>
    </section>

    <section class="mb-20">
        <h2 class="text-2xl font-serif mb-8">Selected work</h2>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($featuredProjects as $project)
                <a href="{{ route('projects.show', $project->slug) }}" wire:navigate class="group block p-6 border border-neutral-200 dark:border-neutral-800 rounded-lg hover:-translate-y-1 hover:shadow-lg hover:border-accent/40 dark:hover:border-accent-dark/40 transition">
                    <h3 class="text-xl font-serif group-hover:text-accent dark:group-hover:text-accent-dark transition-colors">{{ $project->title }}</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">{{ $project->summary }}</p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach (($project->tech_stack ?? []) as $t)
                            <span class="text-xs font-mono px-2 py-1 rounded bg-accent-light dark:bg-accent/10 text-accent dark:text-accent-dark">{{ $t }}</span>
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
        <a href="/contact" wire:navigate class="text-accent dark:text-accent-dark hover:underline">Get in touch →</a>
    </section>
</div>
