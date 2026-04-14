<?php

use App\Models\GithubActivityCache;

it('returns the single latest cache row or null', function () {
    expect(GithubActivityCache::latestSnapshot())->toBeNull();

    GithubActivityCache::create([
        'payload' => ['events' => []],
        'fetched_at' => now(),
    ]);

    expect(GithubActivityCache::latestSnapshot())->not->toBeNull();
});
