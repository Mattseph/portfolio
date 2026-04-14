<?php

namespace App\Livewire\Pages;

use App\Models\Experience;
use App\Settings\AboutSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class About extends Component
{
    public function render()
    {
        return view('livewire.pages.about', [
            'about' => app(AboutSettings::class),
            'experiences' => Experience::ordered()->get(),
        ]);
    }
}
