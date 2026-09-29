<?php

namespace Database\Factories;

use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
{
    protected $model = Sensor::class;

    public function definition()
    {
        return [
            'ph' => $this->faker->randomFloat(2, 6.5, 11),
            'suhu' => $this->faker->randomFloat(2, 24, 40),
            // 'salinitas' => $this->faker->randomFloat(2, 15, 30),
            'kekeruhan' => $this->faker->randomFloat(2, 0, 50),
            'created_at' => now(), // Gunakan waktu saat ini
            'updated_at' => now(),
        ];
    }
}
