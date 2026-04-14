<?php

use App\Models\GithubActivityCache;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;

beforeEach(fn () => User::factory()->create(['is_admin' => true]));

it('renders featured projects on home', function () {
    Project::factory()->create(['is_featured' => true, 'is_published' => true, 'title' => 'FeatureOne']);
    Project::factory()->create(['is_featured' => false, 'is_published' => true, 'title' => 'BackgroundTwo']);

    $this->get('/')
        ->assertOk()
        ->assertSee('FeatureOne')
        ->assertDontSee('BackgroundTwo');
});

it('renders the hero headline from settings', function () {
    $this->get('/')->assertSee('Backend engineer who ships.');
});

it('renders skill names in the marquee', function () {
    Skill::factory()->create(['name' => 'Laravel']);
    $this->get('/')->assertSee('Laravel');
});

it('hides github strip when cache is empty', function () {
    $this->get('/')->assertDontSee('Recent activity');
});

it('shows github strip when cache has events', function () {
    GithubActivityCache::create([
        'payload' => ['events' => [['type' => 'PushEvent', 'repo' => 'acme/portfolio', 'at' => '2026-04-12']]],
        'fetched_at' => now(),
    ]);
    $this->get('/')->assertSee('Recent activity')->assertSee('acme/portfolio');
});
