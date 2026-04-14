<?php

namespace App\Livewire\Pages;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Services extends Component
{
    public function render()
    {
        return view('livewire.pages.services', [
            'services' => Service::published()->ordered()->get(),
        ]);
    }
}
