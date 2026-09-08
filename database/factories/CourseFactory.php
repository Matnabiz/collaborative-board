<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // teacher_id will be injected in Seeder
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(20, 200),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
        ];
    }
}
