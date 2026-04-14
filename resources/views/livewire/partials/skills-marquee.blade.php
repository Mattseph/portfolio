<section class="my-20 overflow-hidden" aria-label="Skills">
    <h2 class="sr-only">Tech stack</h2>
    <div class="marquee">
        <div class="marquee-track">
            @foreach ($skills as $s)
                <span class="marquee-item">{{ $s->name }}</span>
            @endforeach
            @foreach ($skills as $s)
                <span class="marquee-item" aria-hidden="true">{{ $s->name }}</span>
            @endforeach
        </div>
    </div>
</section>
