<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
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
            'user_id'          => ['sometimes', 'integer', 'exists:users,id'],
            'name'             => ['sometimes', 'string'],
            'content'          => ['nullable', 'string'],
            'thumb_url'        => ['nullable', 'string'],
            'description'      => ['nullable', 'string'],
            'is_active'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Vui lòng chọn người tạo.',
            'user_id.exists'   => 'Người tạo không tồn tại.',
        ];
    }
}
