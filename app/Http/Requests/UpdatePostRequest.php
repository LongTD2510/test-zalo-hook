<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
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
            'user_id'     => ['sometimes', 'integer', 'exists:users,id'],
            'title'       => ['sometimes', 'string', 'max:255'],
            'content'     => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'details'     => ['nullable', 'json'],
            'thumb_url'   => ['nullable', 'string'],
            'posted_date'   => ['nullable', 'date'],
            'status'      => ['sometimes', 'integer', 'in:0,1'],
            'categories'  => ['nullable', 'array'], // Mảng ID của các category
            'categories.*' => ['integer', 'exists:categories,id'], // Mỗi phần
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists'   => 'Người đăng không tồn tại.',
            'status.in'        => 'Trạng thái chỉ nhận giá trị 0 hoặc 1.',
            'categories.array' => 'Danh sách danh mục phải là một mảng.',
            'categories.*.integer' => 'Mỗi danh mục phải là một ID hợp lệ.',
            'categories.*.exists' => 'Một hoặc nhiều danh mục không tồn tại.',
        ];
    }
}
