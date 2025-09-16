<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'students' => 'required|array',
            'students.*.full_name' => 'required|string',
            'students.*.exam_school_year_id' => 'required|integer|exists:examination_school_years,id',
            'students.*.room' => 'required|string',
            'students.*.location' => 'required|string',
            'students.*.math' => 'required|numeric',
            'students.*.english' => 'required|numeric',
            'students.*.literature' => 'required|numeric'
        ];
    }
}
