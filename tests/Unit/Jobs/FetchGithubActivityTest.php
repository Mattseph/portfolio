<?php

use App\Jobs\FetchGithubActivity;
use App\Models\GithubActivityCache;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => config(['services.github.token' => 'fake-token', 'services.github.username' => 'testuser']));

it('writes a fresh snapshot on success', function () {
    Http::fake([
        'api.github.com/*' => Http::response([
            ['type' => 'PushEvent', 'repo' => ['name' => 'acme/test'], 'created_at' => '2026-04-01T00:00:00Z'],
        ], 200),
    ]);

    (new FetchGithubActivity)->handle();

    $snap = GithubActivityCache::latestSnapshot();
    expect($snap)->not->toBeNull()
        ->and($snap->payload['events'])->toHaveCount(1)
        ->and($snap->payload['events'][0]['type'])->toBe('PushEvent');
});

it('does not overwrite cache on API failure', function () {
    GithubActivityCache::create(['payload' => ['events' => [['type' => 'old']]], 'fetched_at' => now()->subHour()]);
    Http::fake(['api.github.com/*' => Http::response('nope', 500)]);

    (new FetchGithubActivity)->handle();

    expect(GithubActivityCache::latestSnapshot()->payload['events'][0]['type'])->toBe('old');
});

it('tolerates empty cache on first run with failure', function () {
    Http::fake(['api.github.com/*' => Http::response('err', 500)]);

    (new FetchGithubActivity)->handle();

    expect(GithubActivityCache::count())->toBe(0);
});
