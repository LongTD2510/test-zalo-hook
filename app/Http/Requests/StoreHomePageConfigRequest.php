<?php

namespace App\Http\Requests;

use App\Traits\HandleFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomePageConfigRequest extends FormRequest
{
    use HandleFailedValidation;

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
            'key'   => [
                'required',
                'string',
                'max:255',
                'regex:/^homepage_[a-z0-9_]+$/i'
            ],
            'value' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Trường key là bắt buộc.',
            'key.string'   => 'Trường key phải là chuỗi.',
            'key.max'      => 'Trường key không được vượt quá 255 ký tự.',
            'key.regex'    => 'Trường key phải bắt đầu bằng "homepage_" và chỉ chứa chữ cái, số, gạch dưới.',
            'value.required' => 'Trường value là bắt buộc.',
            'value.array'    => 'Trường value phải là một mảng JSON hợp lệ.',
        ];
    }
}
