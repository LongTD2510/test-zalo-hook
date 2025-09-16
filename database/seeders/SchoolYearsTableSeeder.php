<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\LOG;


class SchoolYearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('school_years')->insert([
            ['year' => '2022', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2023', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2024', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2025', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2026', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2027', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2028', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2029', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2030', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2031', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2032', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2033', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2034', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2035', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2036', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2037', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2038', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['year' => '2039', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);
        
    }
}
