<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $devices = [
            'Laptop',
            'Desktop',
            'Monitor',
            'Printer',
            'Router',
            'Switch',
            'Server',
            'Tablet',
            'Smartphone',
            'Projector',
            'Camera',
            'Headset',
            'Microphone',
            'Speaker',
            'External Hard Drive',
            'USB Flash Drive',
            'Docking Station',
            'Webcam',
            'Smartwatch',
            'Fitness Tracker',
            'VR Headset',
            'Network Attached Storage (NAS)',
            'Firewall Appliance',
            'Access Point',
            'KVM Switch',
            'Graphics Tablet',
            '3D Printer',
            'Digital Whiteboard',
            'Smart Home Hub',
            'E-Reader',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($devices),
            'description' => $this->faker->sentence(),
        ];
    }
}
