<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id will usually be set in Seeder
            'bio' => fake()->paragraph(3),
            'hourly_rate' => fake()->numberBetween(10, 100),
            'experience_years' => fake()->numberBetween(1, 20),
            'languages' => fake()->randomElement(['English', 'German', 'French']),
        ];
    }
}
