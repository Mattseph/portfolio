<?php

use App\Livewire\Pages\Contact;
use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    RateLimiter::clear('contact:' . request()->ip());
});

it('validates required fields', function () {
    Livewire::test(Contact::class)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'message']);
});

it('stores a valid submission and sends mail', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Alice')
        ->set('email', 'a@b.com')
        ->set('subject', 'Hi')
        ->set('message', 'Hello there')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(1);
    Mail::assertQueued(ContactNotification::class);
});

it('rejects bots via honeypot', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Alice')
        ->set('email', 'a@b.com')
        ->set('subject', 'Hi')
        ->set('message', 'Hello')
        ->set('website', 'spam.example')   // honeypot field
        ->call('submit')
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingQueued();
});

it('rate limits a second submission within 60s', function () {
    $first = Livewire::test(Contact::class)
        ->set('name', 'Alice')->set('email', 'a@b.com')
        ->set('subject', 'Hi')->set('message', 'msg1')
        ->call('submit');

    $second = Livewire::test(Contact::class)
        ->set('name', 'Alice')->set('email', 'a@b.com')
        ->set('subject', 'Hi')->set('message', 'msg2')
        ->call('submit')
        ->assertHasErrors(['message']);

    expect(ContactMessage::count())->toBe(1);
});
