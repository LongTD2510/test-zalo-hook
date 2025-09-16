<?php

namespace App\Services;

use App\Enums\FileType;
use App\Enums\TemplateEnums;
use App\Models\Template;
use App\RepositoryInterfaces\TemplateRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TemplateService
{
    use ApiResponse;
    protected const LIMIT = 1000;
    private $templateRepository;

    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function getTemplate($request): JsonResponse
    {
        try {
            $args = $request->all();
            $perPage = $request->query('per_page');
            $limit = $request->query('limit') ?? self::LIMIT;
            $mode = $request->query('mode', 'full');

            $query = !empty($args['keyword'])
                ? $this->templateRepository->searchTemplates($args, $mode)
                : $this->templateRepository->listAll($args);

            $templates = $perPage
                ? $query->paginate($perPage, ['*'], 'page')
                : $query->limit($limit)->get();

            return $this->successResponse($templates);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function storeTemplate(array $data): JsonResponse
    {
        try {
            if (!empty($data['content'])) {
                preg_match_all('/{{\s*(\w+)\s*}}/', $data['content'], $matches);
                $params = $matches[1] ?? [];

                $result = [];
                foreach ($params as $param) {
                    $result[] = [
                        'name'       => $param,
                    ];
                }
                $data['params'] = json_encode($result, JSON_UNESCAPED_UNICODE);
            }

            $timestamp = now()->format('YmdHis');
            $data = array_merge($data, [
                'code' => strtoupper(Str::slug($data['name'] . '_' . $timestamp, '_')),
                'channel' => TemplateEnums::TEMPLATE_CHANNEL_CUSTOM_OA,
                'status' => $data['is_active'] ? TemplateEnums::TEMPLATE_STATUS_ENABLE : TemplateEnums::TEMPLATE_STATUS_DISABLE,
            ]);
            $template = Template::query()->create($data);
            return $this->successResponse($template, 'Template created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function updateTemplate(array $data, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $template = Template::query()->find($id);
            if (!$template) {
                return $this->errorResponse(['message' => 'Template not found'], 404);
            }

            if (!empty($data['content'])) {
                preg_match_all('/{{\s*(\w+)\s*}}/', $data['content'], $matches);
                $params = $matches[1] ?? [];

                $result = [];
                foreach ($params as $param) {
                    $result[] = [
                        'name' => $param,
                    ];
                }
                $data['params'] = json_encode($result, JSON_UNESCAPED_UNICODE);
            }
            if (isset($data['is_active'])) {
                $data['status'] = $data['is_active'] ? TemplateEnums::TEMPLATE_STATUS_ENABLE : TemplateEnums::TEMPLATE_STATUS_DISABLE;
            }
            $template->update($data);
            DB::commit();

            return $this->successResponse($template, 'Template updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteTemplate($request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids) || empty($ids)) {
                return $this->errorResponse(['message' => 'No IDs provided'], 400);
            }
            DB::beginTransaction();
            Template::whereIn('id', $ids)->each(function ($template) {
                $template->delete();
            });
            DB::commit();

            return $this->successResponse(null, 'Template deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function uploadImage($data)
    {
        $file = Arr::get($data, 'image');
        $storageService = new StorageService('r2');
        $imageUrl = $storageService->uploadTempFiles([$file], FileType::TEMPLATE, null);

        if (empty($imageUrl)) {
            return $this->errorResponse(['message' => 'Image upload failed'], 500);
        }

        return $this->successResponse($imageUrl, 'Image upload job dispatched successfully');
    }
}