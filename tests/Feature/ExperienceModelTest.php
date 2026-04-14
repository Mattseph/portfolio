<?php

use App\Models\Experience;

it('treats null ended_at as "present"', function () {
    $e = Experience::factory()->create(['ended_at' => null]);
    expect($e->fresh()->ended_at)->toBeNull();
});
