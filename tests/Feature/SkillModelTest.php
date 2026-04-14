<?php

use App\Models\Skill;

it('creates a skill with an enum category', function () {
    $s = Skill::factory()->create(['category' => 'framework']);
    expect($s->fresh()->category)->toBe('framework');
});

it('scopes ordered results', function () {
    Skill::factory()->create(['sort_order' => 2, 'name' => 'B']);
    Skill::factory()->create(['sort_order' => 1, 'name' => 'A']);
    expect(Skill::ordered()->pluck('name')->all())->toBe(['A', 'B']);
});
