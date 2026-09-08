<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{

    public function run(): void
    {
        User::where('role', 'teacher')->each(function ($user) {
            Teacher::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }

}
