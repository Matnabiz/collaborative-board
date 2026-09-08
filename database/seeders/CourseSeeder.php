<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::with('user')->each(function ($teacher) {
            Course::factory()
                ->count(rand(2, 4))
                ->create([
                    'teacher_id' => $teacher->id,
                ]);
        });
    }
}
