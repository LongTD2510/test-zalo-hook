<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\NotifyMonthQuarter;
class NotifyMonthQuarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $years = [2024, 2025, 2026, 2027];
            $months = [1,2,3,4,5,6,7,8,9,10,11,12];
            $weeks = [1,2,3,4];
            foreach ($years as $year) {
                foreach ($months as $month) {
                    foreach ($weeks as $week) {
                        $name = 'Tuần ' . $week . ' tháng ' . $month . ' năm ' . $year;
                        $slug = "$week.w-$month.m-$year.y";
                        NotifyMonthQuarter::create([
                            'name' => $name,
                            'slug' => $slug,
                            'week' => $week,
                            'month' => $month,
                            'year' => $year
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
