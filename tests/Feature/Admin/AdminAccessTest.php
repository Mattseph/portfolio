<?php

use App\Models\User;

it('redirects guests from /admin', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('blocks non-admin users', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('allows admin users', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->get('/admin')->assertOk();
});
