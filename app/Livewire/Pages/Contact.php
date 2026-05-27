<?php

namespace App\Livewire\Pages;

use App\Settings\SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Contact extends Component
{
    public function render()
    {
        $site = app(SiteSettings::class);

        return view('livewire.pages.contact', [
            'github'   => $site->github_url,
            'linkedin' => $site->linkedin_url,
            'twitter'  => $site->twitter_url,
        ]);
    }
}
