<?php

namespace Database\Factories;

use App\AssetStatusEnum;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryId = Category::inRandomOrder()->first()->id ?? Category::factory();
        $location = Location::inRandomOrder()->first()->id ?? Location::factory();
        $manufacturer = Manufacturer::inRandomOrder()->first()->id ?? Manufacturer::factory();
        $user = User::inRandomOrder()->first()->id ?? User::factory();

        $statuses = [
            AssetStatusEnum::Deployed,
            AssetStatusEnum::InStorage,
            AssetStatusEnum::Maintenance,
            AssetStatusEnum::Retired,
            AssetStatusEnum::Broken,
        ];

        return [
            'asset_tag' => 'AST-' . $this->faker->unique()->randomNumber(5, true),
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Laptop', 'Desktop', 'Monitor', 'Printer', 'Router']),
            'serial_number' => strtoupper($this->faker->unique()->bothify('SN-??#####')),
            'model_name' => $this->faker->bothify('Model-??##'),
            'purchase_date' => $this->faker->dateTimeBetween('2010-01-01', 'now')->format('Y-m-d'),
            'purchase_price' => $this->faker->randomFloat(2, 100, 5000),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional()->paragraph(),
            'category_id' => $categoryId,
            'location_id' => $location,
            'manufacturer_id' => $manufacturer,
            'assigned_to_user_id' => $this->faker->boolean(70) ? $user : null,
        ];
    }
}
