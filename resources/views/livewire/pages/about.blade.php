<div>
    <section class="mt-12 grid md:grid-cols-3 gap-10">
        @if ($about->profile_image_path)
            <img src="{{ asset('storage/' . $about->profile_image_path) }}" alt="{{ $about->full_name }}" class="rounded-lg w-full max-w-xs">
        @endif
        <div class="md:col-span-2">
            <h1 class="text-4xl font-serif mb-1">{{ $about->full_name }}</h1>
            @if ($about->location)
                <p class="flex items-center gap-1.5 text-sm text-neutral-500 dark:text-neutral-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 shrink-0">
                        <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.757.433c.118.06.225.106.313.138l.029.01.006.003zM10 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd"/>
                    </svg>
                    {{ $about->location }}
                </p>
            @endif
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
