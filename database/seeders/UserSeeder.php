<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@aims.com',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::SUPER_ADMIN,
        ]);

        User::create([
            'name' => 'Inventory Manager',
            'email' => 'manager@aims.com',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::INVENTORY_MANAGER,
        ]);

        User::create([
            'name' => 'Inventory User',
            'email' => 'user@aims.com',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::INVENTORY_USER,
        ]);
    }
}
