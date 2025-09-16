<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExaminationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('examinations')->insert([
            ['exam' => 'Thi Khảo Sát Đầu Vào', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['exam' => 'Thi thử đợt 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['exam' => 'Thi thử đợt 2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
