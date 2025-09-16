<?php

namespace App\Http\Requests;

use App\Traits\HandleFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'user_id'     => ['required', 'integer', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'details'     => ['nullable', 'json'],
            'thumb_url'   => ['nullable', 'string'],
            'posted_date'   => ['nullable', 'date'],
            'status'      => ['required', 'integer', 'in:0,1'], // 0: inactive, 1: active
            'categories'  => ['nullable', 'array'], // Mảng ID của các category
            'categories.*' => ['integer', 'exists:categories,id'], // Mỗi phần
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Vui lòng chọn người đăng.',
            'user_id.exists'   => 'Người đăng không tồn tại.',
            'title.required'   => 'Tiêu đề không được để trống.',
            'content.required' => 'Nội dung không được để trống.',
            'status.in'        => 'Trạng thái chỉ nhận giá trị 0 hoặc 1.',
            'categories.array' => 'Danh sách danh mục phải là một mảng.',
            'categories.*.integer' => 'Mỗi danh mục phải là một ID hợp lệ.',
            'categories.*.exists' => 'Một hoặc nhiều danh mục không tồn tại.',
        ];
    }
}
