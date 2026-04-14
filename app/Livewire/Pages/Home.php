<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use App\Settings\HomeSettings;
use App\Settings\SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.home', [
            'home' => app(HomeSettings::class),
            'site' => app(SiteSettings::class),
            'featuredProjects' => Project::published()->featured()->ordered()->take(4)->get(),
        ]);
    }
}
