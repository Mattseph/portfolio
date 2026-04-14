<?php

use App\Models\Project;

it('creates a project with tech stack as an array and slug unique', function () {
    $p = Project::factory()->create([
        'slug' => 'laravel-billing',
        'tech_stack' => ['Laravel', 'MySQL', 'Redis'],
    ]);

    expect($p->fresh()->tech_stack)->toBe(['Laravel', 'MySQL', 'Redis'])
        ->and(Project::where('slug', 'laravel-billing')->count())->toBe(1);
});

it('filters published and featured', function () {
    Project::factory()->create(['is_published' => false]);
    Project::factory()->create(['is_published' => true, 'is_featured' => true]);
    Project::factory()->create(['is_published' => true, 'is_featured' => false]);

    expect(Project::published()->count())->toBe(2)
        ->and(Project::published()->featured()->count())->toBe(1);
});
