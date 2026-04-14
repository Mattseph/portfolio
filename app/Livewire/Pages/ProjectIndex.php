<?php

namespace App\Livewire\Pages;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProjectIndex extends Component
{
    #[Url(as: 'tech')]
    public ?string $filter = null;

    public function render()
    {
        $projects = Project::published()->ordered();

        if ($this->filter) {
            $projects->whereJsonContains('tech_stack', $this->filter);
        }

        $allTags = Project::published()->get()
            ->flatMap(fn ($p) => $p->tech_stack ?? [])
            ->unique()->sort()->values();

        return view('livewire.pages.project-index', [
            'projects' => $projects->get(),
            'allTags' => $allTags,
        ]);
    }
}
