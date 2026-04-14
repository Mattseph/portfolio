<?php

namespace App\Livewire\Partials;

use App\Models\Skill;
use Livewire\Component;

class SkillsMarquee extends Component
{
    public function render()
    {
        return view('livewire.partials.skills-marquee', [
            'skills' => Skill::ordered()->get(),
        ]);
    }
}
