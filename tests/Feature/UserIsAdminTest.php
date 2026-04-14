<?php

use App\Models\User;

it('stores and exposes is_admin as boolean', function () {
    $user = User::factory()->create(['is_admin' => true]);
    expect($user->fresh()->is_admin)->toBeTrue();
});
