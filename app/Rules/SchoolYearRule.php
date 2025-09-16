<?php

namespace App\Rules;

use App\Models\SchoolYear;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class SchoolYearRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value) || $value == 'all') { return true; }
        
        $year = SchoolYear::where('year', $value)->exists();
        if(!$year){
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'Year not found';
    }
}
