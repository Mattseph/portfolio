<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);
        return [
            'slug' => Str::slug($title),
            'title' => rtrim($title, '.'),
            'summary' => $this->faker->sentence(12),
            'role' => 'Backend engineer',
            'started_at' => now()->subMonths(rand(3, 24)),
            'ended_at' => now()->subMonths(rand(0, 2)),
            'tech_stack' => $this->faker->randomElements(['Laravel', 'PHP', 'MySQL', 'Redis', 'Livewire', 'Filament', 'Docker'], 4),
            'problem' => '<p>' . $this->faker->paragraph() . '</p>',
            'approach' => '<p>' . $this->faker->paragraph() . '</p>',
            'challenges' => '<p>' . $this->faker->paragraph() . '</p>',
            'outcome' => '<p>' . $this->faker->paragraph() . '</p>',
            'repo_url' => 'https://github.com/example/' . Str::slug($title),
            'demo_url' => null,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
