<?php

use App\Models\User;

it('renders dashboard widgets for admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin')
        ->assertOk()
        ->assertSee('Unread messages')
        ->assertSee('Available for work');
});
