<?php

namespace App\Services;

use App\Enums\FileType;
use App\Jobs\UploadImageContent;
use App\Models\Category;
use App\Models\Config;
use App\Models\Examination;
use App\Models\Teacher;
use App\RepositoryInterfaces\ConfigRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HomePageService
{
    use ApiResponse;
    protected const LIMIT = 1000;
    private $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function uploadImage(array $data)
    {
        $storageService = new StorageService('r2');
        $imageUrl = $storageService->uploadTempFiles($data, FileType::HOME_PAGE, null);

        if (empty($imageUrl)) {
            return $this->errorResponse(['message' => 'Image upload failed'], 500);
        }

        return $this->successResponse($imageUrl, 'Image upload job dispatched successfully');
    }

    public function listingHomePageConfig($request)
    {
        try {
            $args = $request->all();
            $args["key_like"] = "homepage_";
            $limit = $request->query('limit') ?? self::LIMIT;

            $configs = $this->configRepository->getWhere($args, $limit);

            // Nếu không có dữ liệu thì tạo mặc định
            if ($configs->isEmpty()) {
                $this->createDefaultHomepageConfigs();

                // Lấy lại dữ liệu sau khi tạo
                $configs = $this->configRepository->getWhere($args, $limit);
            }

            //Lấy về tất cả giáo viên có trong homepage_teachers
            $configs = $configs->map(function ($config) {
                if ($config->key === 'homepage_teachers') {
                    // Đảm bảo value là array
                    $value = is_string($config->value)
                        ? json_decode($config->value, true)
                        : $config->value;

                    if (isset($value['teacher_ids']) && is_array($value['teacher_ids'])) {
                        // Lấy danh sách giáo viên theo id
                        $teachers = Teacher::query()->whereIn('id', $value['teacher_ids'])->get();

                        // Gắn thêm teachers vào value
                        $value['teachers'] = $teachers->map(function ($teacher) {
                            return [
                                'id' => $teacher->id,
                                'name' => $teacher->name,
                                'age' => $teacher->age,
                                'bullet_point' => $teacher->bullet_point,
                                'motto' => $teacher->motto,
                                'quote' => $teacher->quote,
                                'viewpoint' => $teacher->viewpoint,
                                'file_url' => $teacher->file_url,
                                'short_description' => $teacher->short_description
                            ];
                        });
                    }

                    $config->value = $value;
                }

                return $config;
            });

            return $this->successResponse($configs);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function store($data)
    {
        try {
            $config = Config::create($data);
            return $this->successResponse($config, 'Config created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function update($data, $key)
    {
        try {
            $value = $data['value'] ?? null;
            $args = [
                'key' => $key,
            ];
            $config = $this->configRepository->findWhere($args);
            if (is_null($config)) {
                return $this->errorResponse(['message' => 'Config not found'], 404);
            }

            $config->update([
                'value' => $value,
            ]);
            return $this->successResponse($config, 'Config updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($key)
    {
        try {
            $args = [
                'key' => $key,
            ];
            $config = $this->configRepository->findWhere($args);
            if (is_null($config)) {
                return $this->errorResponse(['message' => 'Config not found'], 404);
            }
            $config->delete();
            return $this->successResponse(null, 'Config deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function getConfigByKey($key)
    {
        try {
            $args = [
                'key' => $key,
            ];
            $config = $this->configRepository->findWhere($args);
            if (is_null($config)) {
                return $this->errorResponse(['message' => 'Config not found'], 404);
            }
            return $this->successResponse($config);
        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function restoreDefaultConfig($key)
    {
        $availableKeys = [
            'homepage_banner',
            'homepage_intro',
            'homepage_course',
            'homepage_review',
            'homepage_teachers',
        ];

        if (!in_array($key, $availableKeys)) {
            return $this->errorResponse(['message' => 'Invalid config key'], 400);
        }

        $defaultConfigs = $this->defaultHomepageConfigs();

        // Lấy cấu hình hiện tại từ DB
        $config = Config::where('key', $key)->withTrashed()->first();

        if ($config) {
            // Nếu đã bị soft delete → phục hồi
            if ($config->trashed()) {
                $config->restore();
            }

            // Gán lại value mặc định
            $config->value = $defaultConfigs[$key];
            $config->save();
        } else {
            // Nếu chưa tồn tại → tạo mới
            Config::create([
                'key' => $key,
                'value' => $defaultConfigs[$key]
            ]);
        }

        return $this->successResponse($config, 'Default config returned successfully');
    }

    private function defaultHomepageConfigs()
    {
        return [
            'homepage_banner' => [
                'images' => [
                    "https://r2.clcntt.edu.vn/1755521745.4493.webp"
                ]
            ],
            'homepage_intro' => [
                'big_title_1' => 'Giới thiệu về lớp ôn Chất lượng cao Nguyễn Tất Thành',
                'big_title_2' => null,
                'content_1' => 'Lớp ôn Chất lượng cao Nguyễn Tất Thành là một điểm đến lý tưởng cho các bạn học sinh chuẩn bị cho những kỳ thi quan trọng. Với đội ngũ giáo viên giàu kinh nghiệm và phương pháp giảng dạy chuyên nghiệp, chúng tôi cam kết trang bị cho học sinh những kiến thức và kỹ năng vững chắc, giúp học sinh tự tin vượt qua mọi thách thức trong hành trình học tập.',
                'content_2' => 'Chúng tôi không chỉ tập trung vào việc truyền đạt kiến thức một cách hiệu quả mà còn khuyến khích sự phát triển toàn diện cho học sinh, từ khả năng tư duy, logic đến kỹ năng làm việc nhóm và giao tiếp. Với một môi trường học tập tích cực và đầy động lực, lớp ôn Chất lượng cao Nguyễn Tất Thành sẽ là nơi tạo ra những học sinh tự tin, đam mê và thành công.',
                'image_url' => 'https://r2.clcntt.edu.vn/1755521843.3768.webp',
                'button_url' => 'https://clcntt.edu.vn/intro',
                'description' => null
            ],
            'homepage_course' => [
                'big_title_1' => 'Hệ thống khoá học tại lớp ôn Chất lượng cao Nguyễn Tất Thành',
                'big_title_2' => null,
                'content' => [
                    [
                        'id' => 'kth',
                        'url' => 'https://clcntt.edu.vn/schedule/tieu_học',
                        'image_url' => 'https://r2.clcntt.edu.vn/1755521917.7892.webp',
                        'title' => 'Khối tiểu học',
                        'content' => 'Xây dựng nền tảng, nâng cao kiến thức Tiểu học để bồi dưỡng học sinh thi vào các trường THCS Chuyên, Chất lượng cao.'
                    ],
                    [
                        'id' => 'kthcs',
                        'url' => 'https://clcntt.edu.vn/schedule/thcs',
                        'image_url' => 'https://r2.clcntt.edu.vn/1755521964.5928.webp',
                        'title' => 'Khối trung học cơ sở',
                        'content' => 'Củng cố kiến thức nền tảng, đưa ra phương hướng và kế hoạch phù hợp với năng lực học sinh, giúp các em đặt mục tiêu trúng tuyển vào trường Chuyên, Chất lượng cao.'
                    ],
                    [
                        'id' => 'kthpt',
                        'url' => 'https://clcntt.edu.vn/schedule/thpt_2',
                        'image_url' => 'https://r2.clcntt.edu.vn/1755521998.5838.webp',
                        'title' => 'Khối trung học phổ thông',
                        'content' => 'Đánh giá năng lực, đưa ra định hướng và ôn luyện kiến thức cho học sinh bám sát theo cấu trúc đề thi THPT Quốc gia.'
                    ],
                    [
                        'id' => 'lttsa',
                        'url' => 'https://clcntt.edu.vn/schedule/thpt?class=tsa',
                        'image_url' => 'https://r2.clcntt.edu.vn/1755522037.8504.webp',
                        'title' => 'Luyện thi TSA',
                        'content' => 'Ôn luyện kiến thức cho học sinh bám sát cấu trúc đề thi Đánh giá tư duy, giúp các em đạt mục tiêu xét tuyển vào các trường đại học.'
                    ],
                ],
                'button_url' => 'https://clcntt.edu.vn/schedule',
                'description' => null
            ],
            'homepage_review' => [
                'big_title_1' => 'Phụ huynh & học sinh nghĩ gì về lớp ôn',
                'big_title_2' => 'Chất lượng cao Nguyễn Tât Thành',
                'tiktok_urls' => [
                    ['url' => 'https://www.tiktok.com/@loponclc/video/7361469576527219976'],
                    ['url' => 'https://www.tiktok.com/@loponclc/video/7361067770454248722'],
                    ['url' => 'https://www.tiktok.com/@loponclc/video/7362889818461834514'],
                    ['url' => 'https://www.tiktok.com/@loponclc/video/7368830050512276754'],
                ],
                'description' => null
            ],
            'homepage_teachers' => [
                'big_title_1' => 'Đội ngũ giáo viên',
                'big_title_2' => 'KINH NGHIỆM - TÀI NĂNG - TÂM HUYẾT',
                'teacher_ids' => [2, 3, 4, 7],
                'button_url' => 'https://clcntt.edu.vn/teachers',
                'description' => null
            ],
        ];
    }

    /**
     * Tạo config mặc định cho homepage
     */
    protected function createDefaultHomepageConfigs()
    {
        $configs = $this->defaultHomepageConfigs();

        foreach ($configs as $key => $value) {
            Config::create([
                'key'        => $key,
                'value'      => $value, // mảng hoặc object
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
