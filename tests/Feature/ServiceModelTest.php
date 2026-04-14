<?php

use App\Models\Service;

it('scopes published services', function () {
    Service::factory()->create(['is_published' => false]);
    Service::factory()->create(['is_published' => true]);
    expect(Service::published()->count())->toBe(1);
});
