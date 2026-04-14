<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'icon' => 'devicon-laravel-plain',
            'starting_price' => 'From $1,500',
            'sort_order' => 0,
            'is_published' => true,
        ];
    }
}
