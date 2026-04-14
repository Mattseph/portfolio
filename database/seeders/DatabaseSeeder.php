<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Site Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        Project::factory()->count(4)->create(['is_featured' => true]);
        Project::factory()->count(6)->create();

        foreach (['language', 'framework', 'database', 'tool', 'platform'] as $cat) {
            Skill::factory()->count(3)->create(['category' => $cat]);
        }

        Experience::factory()->count(3)->create();
        Service::factory()->count(4)->create();
    }
}
