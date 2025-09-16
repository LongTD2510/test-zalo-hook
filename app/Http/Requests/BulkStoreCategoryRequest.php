<?php

namespace App\Http\Requests;

use App\Rules\ValidBulkParentCategory;
use App\Traits\HandleFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class BulkStoreCategoryRequest extends FormRequest
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
            'categories' => 'array',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.parent_id' => [
                'nullable',
                'integer',
                new ValidBulkParentCategory($this->input('categories')) // Truyền danh sách categories vào rule
            ],
            'categories.*.slug' => 'required|string|max:255',
            'categories.*.thumb_url' => 'nullable|url|max:255',
            'categories.*.status' => 'nullable|integer|in:0,1',
        ];
    }
}
