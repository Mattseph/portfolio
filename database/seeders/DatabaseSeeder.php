<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'matthewjoseph.bilaos@gmail.com'],
            [
                'name' => 'Matthew Bilaos',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        $this->call(PortfolioDataSeeder::class);
    }
}
