<?php

use App\Models\Experience;

it('renders experiences on about page', function () {
    Experience::factory()->create(['company' => 'AcmeCo']);
    $this->get('/about')->assertOk()->assertSee('AcmeCo');
});
