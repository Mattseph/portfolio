<?php

namespace App\Livewire\Pages;

use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Settings\SiteSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Contact extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('required|string|max:150')]
    public string $subject = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    public string $website = ''; // honeypot

    public bool $sent = false;

    public function submit()
    {
        if ($this->website !== '') {
            // silently accept — do nothing, pretend success
            $this->sent = true;
            return;
        }

        $this->validate();

        $ip = request()->ip();
        $key = 'contact:' . $ip;
        if (! RateLimiter::attempt($key, 1, fn () => null, 60)) {
            $this->addError('message', 'Too many submissions. Please wait a minute.');
            return;
        }

        $row = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
        ]);

        Mail::to(app(SiteSettings::class)->email)->queue(new ContactNotification($row));

        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.pages.contact');
    }
}
