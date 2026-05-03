<?php

namespace Database\Factories;

use App\Models\DeviceControl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeviceControl>
 */
class DeviceControlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => 'pompa_'.fake()->unique()->numberBetween(1, 99),
            'mode' => fake()->randomElement(['auto', 'manual']),
            'manual_on' => fake()->boolean(),
            'last_heartbeat' => fake()->dateTimeBetween('-1 hour', 'now'),
        ];
    }
}
