<?php

namespace App\Livewire\Partials;

use App\Models\GithubActivityCache;
use Livewire\Component;

class GithubStrip extends Component
{
    public function render()
    {
        return view('livewire.partials.github-strip', [
            'snapshot' => GithubActivityCache::latestSnapshot(),
        ]);
    }
}
