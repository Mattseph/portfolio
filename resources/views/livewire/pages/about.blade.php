<div>
    <section class="mt-12 grid md:grid-cols-3 gap-10">
        @if ($about->profile_image_path)
            <img src="{{ asset('storage/' . $about->profile_image_path) }}" alt="" class="rounded-lg w-full max-w-xs">
        @endif
        <div class="md:col-span-2">
            <h1 class="text-4xl font-serif mb-6">About</h1>
            <div class="prose dark:prose-invert max-w-none">{!! $about->bio !!}</div>
            @if ($about->cv_pdf_path)
                <a href="{{ asset('storage/' . $about->cv_pdf_path) }}" class="inline-block mt-6 text-accent dark:text-accent-dark hover:underline">Download CV (PDF) →</a>
            @endif
        </div>
    </section>

    <section class="mt-20">
        <h2 class="text-2xl font-serif mb-8">Experience</h2>
        <ol class="space-y-8 border-l-2 border-accent/30 dark:border-accent-dark/30 pl-6">
            @foreach ($experiences as $e)
                <li class="relative">
                    <span class="absolute -left-[1.5625rem] top-1 w-3 h-3 rounded-full bg-accent dark:bg-accent-dark ring-2 ring-white dark:ring-neutral-950"></span>
                    <div class="text-xs font-mono uppercase text-accent dark:text-accent-dark">{{ $e->started_at?->format('Y') }} — {{ $e->ended_at?->format('Y') ?? 'Present' }}</div>
                    <h3 class="text-lg font-serif mt-1">{{ $e->role }}, {{ $e->company }}</h3>
                    @if ($e->description) <div class="prose dark:prose-invert max-w-none mt-2">{!! $e->description !!}</div> @endif
                </li>
            @endforeach
        </ol>
    </section>
</div>
