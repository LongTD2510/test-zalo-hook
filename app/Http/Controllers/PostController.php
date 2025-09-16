<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ApiResponse;

    public function listingPosts(Request $request)
    {
        return app(PostService::class)->getPosts($request);
    }

    public function store(StorePostRequest $request)
    {
        return app(PostService::class)->storePost($request->validated());
    }

    public function update(UpdatePostRequest $request, $id)
    {
        return app(PostService::class)->updatePost($request->validated(), $id);
    }

    public function destroy(Request $request)  
    {
        return app(PostService::class)->deletePost($request);
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        return app(PostService::class)->uploadImage($validator->validated());
    }
}
