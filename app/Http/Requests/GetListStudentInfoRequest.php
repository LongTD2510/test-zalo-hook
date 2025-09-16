<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SchoolYearRule;
use App\Rules\ExamTypeRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponse;

class GetListStudentInfoRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_year' => [
                new SchoolYearRule(),
            ],
            'exam_type' => [
                new ExamTypeRule()
            ],
        ];
    }

    public function failedValidation($validator)
    {
        $response =  $this->errorResponse([
            'message' => $validator->errors()->toJson()
        ], 400);
        throw new HttpResponseException($response);
    }
}
