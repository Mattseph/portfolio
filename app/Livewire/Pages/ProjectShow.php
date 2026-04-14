<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProjectShow extends Component
{
    public Project $project;

    public function mount(string $slug)
    {
        $this->project = Project::published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $previous = Project::published()->ordered()
            ->where('sort_order', '<', $this->project->sort_order)->orderByDesc('sort_order')->first();
        $next = Project::published()->ordered()
            ->where('sort_order', '>', $this->project->sort_order)->first();

        return view('livewire.pages.project-show', compact('previous', 'next'));
    }
}
