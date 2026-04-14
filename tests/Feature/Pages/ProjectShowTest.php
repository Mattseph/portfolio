<?php

use App\Models\Project;

it('renders a published project by slug', function () {
    $p = Project::factory()->create([
        'is_published' => true,
        'slug' => 'billing-v2',
        'title' => 'Billing V2',
        'problem' => '<p>Old billing was slow.</p>',
    ]);

    $this->get("/projects/{$p->slug}")
        ->assertOk()
        ->assertSee('Billing V2')
        ->assertSee('Old billing was slow.', false);
});

it('returns 404 for unpublished projects', function () {
    Project::factory()->create(['is_published' => false, 'slug' => 'hidden']);
    $this->get('/projects/hidden')->assertNotFound();
});
