<section>
    @if ($snapshot && ! empty($snapshot->payload['events'] ?? []))
        <div class="my-16">
            <h2 class="text-sm font-mono uppercase text-neutral-500 tracking-widest mb-4">Recent activity</h2>
            <ul class="grid gap-2 text-sm">
                @foreach (array_slice($snapshot->payload['events'], 0, 5) as $event)
                    <li class="text-neutral-600 dark:text-neutral-400">
                        <span class="font-mono text-xs text-accent dark:text-accent-dark">{{ $event['type'] ?? 'event' }}</span>
                        {{ $event['repo'] ?? '' }}
                        <span class="text-xs text-neutral-500">{{ $event['at'] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
