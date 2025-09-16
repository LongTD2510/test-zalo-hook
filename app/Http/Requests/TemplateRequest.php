<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
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
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'name'             => ['required', 'string'],
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
            'name.required' => 'Trường tên template là bắt buộc.',
        ];
    }
}
