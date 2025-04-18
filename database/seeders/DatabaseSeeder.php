<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userExists = User::where([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->count() > 0;
        if ($userExists) {
            echo "\033[0;31m  The admin user was not added because it already exists in the system\033[0m\n";
        } else {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            echo "\033[0;32m  User admin has been added\033[0m\n";
        }
    }
}
