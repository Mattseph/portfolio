<?php

use App\Models\Service;

it('lists only published services', function () {
    Service::factory()->create(['is_published' => true, 'title' => 'API design']);
    Service::factory()->create(['is_published' => false, 'title' => 'Hidden']);
    $this->get('/services')->assertOk()->assertSee('API design')->assertDontSee('Hidden');
});
