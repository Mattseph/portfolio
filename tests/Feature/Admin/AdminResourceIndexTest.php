<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

it('loads all resource index pages', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/admin/projects',
    '/admin/skills',
    '/admin/experiences',
    '/admin/services',
    '/admin/contact-messages',
]);
