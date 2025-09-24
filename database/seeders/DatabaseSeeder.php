<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        User::factory(20)->create();
        Category::factory(20)->create();
        Manufacturer::factory(20)->create();
        Location::factory(8)->create();
        Asset::factory(200)->create();
    }
}
