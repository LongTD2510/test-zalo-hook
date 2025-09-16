<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidBulkParentCategory implements Rule
{
    private $categories; // Danh sách danh mục được gửi lên

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function passes($attribute, $value): bool
    {
        if (!$value) {
            return true; // Nếu không có parent_id thì hợp lệ
        }

        // Lấy danh sách ID của tất cả danh mục trong request
        $categoryIds = collect($this->categories)->pluck('id')->toArray();

        // Tìm tất cả danh mục con của mỗi danh mục trong request
        $childCategoryIds = DB::table('categories')
            ->whereIn('parent_id', $categoryIds)
            ->pluck('id')
            ->toArray();

        // Nếu parent_id của danh mục mới nằm trong danh sách danh mục con => Không hợp lệ
        return !in_array($value, $childCategoryIds);
    }

    public function message(): string
    {
        return 'Danh mục cha được chọn không hợp lệ ! vì nó đang là danh mục con của một danh mục khác trong danh sách.';
    }
}

