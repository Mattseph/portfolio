<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        return [
            'company' => $this->faker->company(),
            'role' => 'Backend Engineer',
            'location' => 'Remote',
            'started_at' => now()->subYears(2),
            'ended_at' => now()->subMonths(3),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'sort_order' => 0,
        ];
    }
}
