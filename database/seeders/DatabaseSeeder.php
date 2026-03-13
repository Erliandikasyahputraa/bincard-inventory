<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        if (User::where('email', 'admin@bingo.com')->doesntExist()) {
            User::create([
                'name' => 'Dika Admin',
                'email' => 'admin@bingo.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'email_verified_at' => now(),
            ])->assignRole('Admin');
        }
        if (User::where('email', 'pelaksana@example.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Pelaksana',
                'email' => 'pelaksana@example.com',
            ])->assignRole('Pelaksana');
        }
    }
}
