<?php

use App\Models\ContactMessage;

it('scopes unread messages', function () {
    ContactMessage::factory()->create(['is_read' => true]);
    ContactMessage::factory()->count(2)->create(['is_read' => false]);
    expect(ContactMessage::unread()->count())->toBe(2);
});
