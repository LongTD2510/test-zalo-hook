<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Examination;
use App\RepositoryInterfaces\CategoryRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    use ApiResponse;
    protected const LIMIT = 1000;
    private $categoryRepository;
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories(Request $request)
    {
        try {
            $args = $request->all();
            $perPage = $request->query('per_page');
            $limit = $request->query('limit') ?? self::LIMIT;

            $products = $perPage
                ? $this->categoryRepository->paginate($args, $perPage, ['*'], 'page')
                : $this->categoryRepository->getWhere($args, $limit);

            return $this->successResponse($products);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function storeCategory(array $data)
    {
        try {
            $category = Category::query()->create($data);
            return $this->successResponse($category, 'Category created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCategory(array $data, $id)
    {
        try {
            $category = Category::query()->find($id);
            if (!$category) {
                return $this->errorResponse([
                    'message' => 'Category not found'
                ], 404);
            }
            $category->update($data);
            return $this->successResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $category = Category::query()->find($id);
            if (!$category) {
                return $this->errorResponse([
                    'message' => 'Category not found'
                ], 404);
            }
            $category->delete();
            DB::commit();
            return $this->successResponse($category, 'Category deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkStoreCategory(array $data)
    {
        try {
            // Kiểm tra xem 'categories' có tồn tại trong request hay không
            if (!isset($data['categories']) || !is_array($data['categories'])) {
                return $this->errorResponse([
                    'message' => 'Invalid categories data'
                ], 400);
            }

            $categories = Arr::get($data, 'categories', []);
            $insertData = [];
            $updateData = [];
            $updateIds = [];
            $slugs = [];

            if (empty($categories)) {
                //clear all
                Category::query()->delete();
                return $this->successResponse([
                    'status' => true,
                    'message' => 'All categories deleted successfully',
                ], 200);
            }

            foreach ($categories as $category) {
                $id = Arr::get($category, 'id');
                $updateIds[] = $id;
                $slugs[] = Arr::get($category, 'slug');
                if (empty($id)) {
                    $insertData[] = [
                        'name' => Arr::get($category, 'name'),
                        'parent_id' => Arr::get($category, 'parent_id'),
                        'slug' => Arr::get($category, 'slug'),
                        'thumb_url' => Arr::get($category, 'thumb_url'),
                        'status' => Arr::get($category, 'status', 1),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $updateData[] = [
                        'id' => $id,
                        'name' => Arr::get($category, 'name'),
                        'parent_id' => Arr::get($category, 'parent_id'),
                        'slug' => Arr::get($category, 'slug'),
                        'thumb_url' => Arr::get($category, 'thumb_url'),
                        'status' => Arr::get($category, 'status', 1),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            $updateIds = array_unique($updateIds);
            $args = [
                'ids' => $updateIds
            ];
            $allCategorySlugs = $this->categoryRepository->getAll()->pluck('slug')->toArray();
            $categoriesExclude = array_diff($allCategorySlugs, $slugs);
            Category::query()->whereIn('slug', $categoriesExclude)->delete();
            $categories = $this->categoryRepository->getWhere($args);

            // Sử dụng insertOrIgnore để tránh lỗi trùng lặp `slug`
            // Đặc tính của insertOrIgnore
            Category::insertOrIgnore($insertData);
            foreach ($updateData as $update) {
                $category = $categories->where('id', $update['id'])->first();
                if ($category) { // Kiểm tra nếu tìm thấy category
                    $category->name = $update['name'];
                    $category->parent_id = $update['parent_id'];
                    $category->slug = $update['slug'];
                    $category->status = $update['status'];
                    $category->save();
                }
            }
            return $this->successResponse([
                'status' => true,
                'message' => 'Categories stored successfully',
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
