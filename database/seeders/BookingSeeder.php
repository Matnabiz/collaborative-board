<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        foreach ($students as $student) {
            $course = $courses->random();

            Booking::factory()->create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'teacher_id' => $course->teacher_id,
                'status' => 'completed',
            ]);
        }
    }
}
