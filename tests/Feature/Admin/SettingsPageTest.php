<?php

use App\Models\User;

it('loads the settings page for admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin/settings')->assertOk();
});
