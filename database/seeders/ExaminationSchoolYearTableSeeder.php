<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Examination;
use App\Models\SchoolYear;
use App\Models\ExaminationSchoolYear;
class ExaminationSchoolYearTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $exams = Examination::get();
            $years = SchoolYear::get();
            foreach ($exams as $exam) {
                foreach ($years as $year) {
                    $examExisted = ExaminationSchoolYear::query()->where('examination_id', $exam->id)
                    ->where('school_year_id', $year->id)
                    ->exists();
                    if(!$examExisted) {
                        ExaminationSchoolYear::insert([
                            'examination_id'=> $exam->id,
                            'school_year_id'=> $year->id
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            
        }
    }
}
