<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name' => ucfirst($name),
            'category' => $this->faker->randomElement(['language', 'framework', 'database', 'tool', 'platform']),
            'icon' => 'devicon-laravel-plain',
            'proficiency' => rand(3, 5),
            'sort_order' => 0,
        ];
    }
}
