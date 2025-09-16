<?php

namespace App\Http\Requests;

use App\Traits\HandleFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'thumb_url' => 'nullable',
            'status' => 'required|integer|in:0,1', // 0 = inactive, 1 = active
            'is_featured' => 'nullable|integer|in:0,1', // Optional field for featured status
        ];
    }
}
