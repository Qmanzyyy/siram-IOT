<?php

namespace Database\Factories;

use App\Models\Jadwal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jadwal>
 */
class JadwalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->words(3, true),
            'waktu_aktif_pertama' => fake()->time('H:i'),
            'waktu_aktif_kedua' => fake()->optional()->time('H:i'),
            'lama_operasi' => fake()->numberBetween(5, 120),
            'aktif' => fake()->boolean(80),
            'hari' => fake()->randomElements(
                ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'],
                fake()->numberBetween(1, 7)
            ),
        ];
    }
}
