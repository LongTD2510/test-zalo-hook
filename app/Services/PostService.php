<?php

namespace App\Services;

use App\Enums\FileType;
use App\Models\Post;

use App\RepositoryInterfaces\PostRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Pow;

class PostService
{
    use ApiResponse;
    protected const LIMIT = 1000;
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPosts($request): JsonResponse
    {
        try {
            $args = $request->all();
            $perPage = $request->query('per_page');
            $limit = $request->query('limit') ?? self::LIMIT;
            $mode = $request->query('mode', 'full');

            $query = !empty($args['keyword'])
                ? $this->postRepository->searchPosts($args, $mode)
                : $this->postRepository->listAll($args);

            $posts = $perPage
                ? $query->paginate($perPage, ['*'], 'page')
                : $query->limit($limit)->get();

            return $this->successResponse($posts);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function storePost(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data['content'] = removeUnwantedStyles($data['content']);
            $post = Post::query()->create($data);
            if (!empty($data['categories'])) {
                $post->categories()->sync($data['categories']);
            }
            DB::commit();

            return $this->successResponse($post, 'Post created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function updatePost(array $data, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $post = Post::query()->find($id);
            if (!$post) {
                return $this->errorResponse(['message' => 'Post not found'], 404);
            }

            if (isset($data['categories'])) {
                $post->categories()->sync($data['categories']);
            }
            $post->update($data);
            DB::commit();

            return $this->successResponse($post, 'Post updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function deletePost($request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids) || empty($ids)) {
                return $this->errorResponse(['message' => 'No IDs provided'], 400);
            }
            DB::beginTransaction();
            Post::whereIn('id', $ids)->each(function ($post) {
                $post->categories()->detach();
                $post->delete();
            });
            DB::commit();

            return $this->successResponse(null, 'Post deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function uploadImage($data)
    {
        $file = Arr::get($data, 'image');
        $storageService = new StorageService('r2');
        $imageUrl = $storageService->uploadTempFiles([$file], FileType::POST, null);

        if (empty($imageUrl)) {
            return $this->errorResponse(['message' => 'Image upload failed'], 500);
        }

        return $this->successResponse($imageUrl, 'Image upload job dispatched successfully');
    }
}