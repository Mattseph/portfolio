<?php

use App\Livewire\Pages\ProjectIndex;
use App\Models\Project;
use Livewire\Livewire;

it('lists all published projects by default', function () {
    Project::factory()->create(['is_published' => true, 'title' => 'Alpha', 'tech_stack' => ['Laravel']]);
    Project::factory()->create(['is_published' => true, 'title' => 'Beta', 'tech_stack' => ['Redis']]);
    Project::factory()->create(['is_published' => false, 'title' => 'Draft']);

    $this->get('/projects')
        ->assertOk()
        ->assertSee('Alpha')
        ->assertSee('Beta')
        ->assertDontSee('Draft');
});

it('filters by tech stack reactively', function () {
    Project::factory()->create(['is_published' => true, 'title' => 'Alpha', 'tech_stack' => ['Laravel']]);
    Project::factory()->create(['is_published' => true, 'title' => 'Beta', 'tech_stack' => ['Redis']]);

    Livewire::test(ProjectIndex::class)
        ->set('filter', 'Redis')
        ->assertSee('Beta')
        ->assertDontSee('Alpha');
});
