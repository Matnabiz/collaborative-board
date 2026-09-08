<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'status' => fake()->randomElement([
                'pending',
                'accepted',
                'completed',
                'cancelled'
            ]),

            'notes' => fake()->optional()->sentence(),
            'total_price' => fake()->randomFloat(2, 20, 200),
        ];
    }
}
