<?php

namespace App\Jobs;

use App\Models\GithubActivityCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGithubActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $user = config('services.github.username');
        $token = config('services.github.token');

        $request = Http::withHeaders(['Accept' => 'application/vnd.github+json']);
        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/users/{$user}/events/public");

        if ($response->failed()) {
            Log::warning('FetchGithubActivity: API failed', ['status' => $response->status()]);
            return;
        }

        $events = collect($response->json())
            ->take(10)
            ->map(fn ($e) => [
                'type' => $e['type'] ?? 'event',
                'repo' => $e['repo']['name'] ?? null,
                'at' => $e['created_at'] ?? null,
            ])
            ->values()
            ->all();

        $cache = GithubActivityCache::first() ?? new GithubActivityCache;
        $cache->payload = ['events' => $events];
        $cache->fetched_at = now();
        $cache->save();
    }
}
