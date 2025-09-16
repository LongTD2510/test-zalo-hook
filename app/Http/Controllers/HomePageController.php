<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomePageConfigRequest;
use App\Http\Requests\UpdateHomePageConfigRequest;
use App\Services\HomePageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageController extends Controller
{
    use ApiResponse;
    public function listingConfig(Request $request)
    {
        return app(HomePageService::class)->listingHomePageConfig($request);
    }

    public function store(StoreHomePageConfigRequest $request)
    {
        return app(HomePageService::class)->store($request->all());
    }

    public function update(UpdateHomePageConfigRequest $request, $key)
    {
        return app(HomePageService::class)->update($request->all(), $key);
    }

    public function destroy($key)
    {
        return app(HomePageService::class)->destroy($key);
    }

    public function getConfigByKey($key)
    {
        return app(HomePageService::class)->getConfigByKey($key);
    }

    public function restoreDefaultConfig($key)
    {
        return app(HomePageService::class)->restoreDefaultConfig($key);
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // validate mỗi phần tử trong mảng images
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // max 2MB / ảnh (tuỳ chỉnh)
        ], [
            'images.required'   => 'Vui lòng chọn ít nhất một ảnh.',
            'images.array'      => 'Dữ liệu ảnh không hợp lệ.',
            'images.*.image'    => 'File phải là ảnh hợp lệ.',
            'images.*.mimes'    => 'Ảnh phải thuộc định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max'      => 'Ảnh không được lớn hơn 2MB.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            // gọi service để upload (truyền toàn bộ mảng images)
            return app(HomePageService::class)->uploadImage($request->file('images'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
