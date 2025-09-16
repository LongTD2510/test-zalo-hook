<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStoreCategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function listingCategories(Request $request)
    {
        return app(CategoryService::class)->getCategories($request);
    }

    public function store(StoreCategoryRequest $request)
    {
        return app(CategoryService::class)->storeCategory($request->validated());
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        return app(CategoryService::class)->updateCategory($request->validated(), $id);
    }

    public function destroy($id)
    {
        return app(CategoryService::class)->deleteCategory($id);
    }

    public function bulkStore(BulkStoreCategoryRequest $request)
    {
        return app(CategoryService::class)->bulkStoreCategory($request->all());
    }
}
