<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class StudentInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_information')
            ->insert
            ([
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Nguyen Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2016-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Nguyen Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2016-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Nguyen Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2016-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Pham Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2016-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Tran Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2015-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'student_id' => Str::random(6),
                    'full_name' => 'Tran Van ' . Str::random(5),
                    'exam_school_year_id' => random_int(1, 8),
                    'room' => Str::random(3),
                    'location' => 'Hanoi',
                    'math' => random_int(0, 10),
                    'english' => random_int(0, 10),
                    'literature' => random_int(0, 10),
                    'birth_date' => new Carbon('2014-01-23 11:53:20'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
    }
}
