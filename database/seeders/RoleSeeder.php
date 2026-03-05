<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pelaksana', 'guard_name' => 'web']);

        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin && ! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        $pelaksana = User::where('email', 'pelaksana@example.com')->first();
        if ($pelaksana && ! $pelaksana->hasRole('Pelaksana')) {
            $pelaksana->assignRole('Pelaksana');
        }
    }
}
