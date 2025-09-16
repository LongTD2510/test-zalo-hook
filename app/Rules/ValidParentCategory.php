<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidParentCategory implements Rule
{
    private int $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function passes($attribute, $value): bool
    {
        if (!$value) {
            return true; // Nếu không có parent_id thì hợp lệ
        }

        // Không cho phép parent_id bằng chính ID của category
        if ($value == $this->categoryId) {
            return false;
        }

        // Lấy danh sách ID của tất cả danh mục con trực tiếp
        $childCategoryIds = DB::table('categories')
            ->where('parent_id', $this->categoryId)
            ->pluck('id')
            ->toArray();

        // Nếu parent_id mới nằm trong danh sách con -> Không hợp lệ
        return !in_array($value, $childCategoryIds);
    }

    public function message(): string
    {
        return 'failed parent_id validation';
    }
}
