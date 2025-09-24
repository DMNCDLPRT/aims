<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $officeLocation = [
            'Main Office - Floor 1',
            'AnnexBuilding - IT Department',
            'Remote - Home Office',
            'Warehouse - Storage Area',
            'Data Center - Rack 5',
            'Branch Office - Floor 2',
            'Headquarters - Floor 3',
            'Remote - Co-working Space',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($officeLocation),
            'address' => $this->faker->address(),
        ];
    }
}
