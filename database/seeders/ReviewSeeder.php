<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::where('status', 'completed')->get();

        foreach ($bookings as $booking) {
            Review::factory()->create([
                'booking_id' => $booking->id,
                'rating' => rand(3, 5),
            ]);
        }
    }
}
