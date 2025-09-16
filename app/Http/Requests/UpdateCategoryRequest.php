<?php

namespace App\Http\Requests;

use App\Rules\ValidParentCategory;
use App\Traits\HandleFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    use HandleFailedValidation; // Assuming you have a trait to handle failed validation
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
            'name' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                new ValidParentCategory($this->route('id')) // Lấy ID từ route
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->route('id')), // Ignore ID đang update
            ],
            'thumb_url' => 'nullable',
            'status' => 'nullable|integer|in:0,1',
            'is_featured' => 'nullable|integer|in:0,1',
        ];
    }
}
