<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Examination;
class ExamTypeRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value) || $value == 'all') { return true; }
        $exam = Examination::where('id', $value)->exists();
        if(!$exam){
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'Exam not found';
    }
}
