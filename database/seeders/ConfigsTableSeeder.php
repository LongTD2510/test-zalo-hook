<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'homepage_banner',
                'value' => json_encode([
                    'image_url' => 'https://r2.nguoiviettainga.ru/1755188999.1742.webp'
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'homepage_intro',
                'value' => json_encode([
                    'title' => 'Giới thiệu về lớp ôn Chất lượng cao Nguyễn Tất Thành',
                    'content_1' => 'Lớp ôn Chất lượng cao Nguyễn Tất Thành là một điểm đến lý tưởng cho các bạn học sinh chuẩn bị cho những kỳ thi quan trọng. Với đội ngũ giáo viên giàu kinh nghiệm và phương pháp giảng dạy chuyên nghiệp, chúng tôi cam kết trang bị cho học sinh những kiến thức và kỹ năng vững chắc, giúp học sinh tự tin vượt qua mọi thách thức trong hành trình học tập.',
                    'content_2' => 'Chúng tôi không chỉ tập trung vào việc truyền đạt kiến thức một cách hiệu quả mà còn khuyến khích sự phát triển toàn diện cho học sinh, từ khả năng tư duy, logic đến kỹ năng làm việc nhóm và giao tiếp. Với một môi trường học tập tích cực và đầy động lực, lớp ôn Chất lượng cao Nguyễn Tất Thành sẽ là nơi tạo ra những học sinh tự tin, đam mê và thành công.',
                    'button_url' => 'https://clcntt.edu.vn/intro'
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'homepage_course',
                'value' => json_encode([
                    'big_title' => 'Hệ thống khoá học tại lớp ôn Chất lượng cao Nguyễn Tất Thành',
                    'content' => [
                        [
                            'image_url' => 'https://r2.nguoiviettainga.ru/1755271537.8732.webp',
                            'title' => 'Khối tiểu học',
                            'content' => 'Xây dựng nền tảng, nâng cao kiến thức Tiểu học để bồi dưỡng học sinh thi vào các trường THCS Chuyên, Chất lượng cao.'
                        ],
                        [
                            'image_url' => 'https://r2.nguoiviettainga.ru/1755271606.2876.webp',
                            'title' => 'Khối trung học cơ sở',
                            'content' => 'Củng cố kiến thức nền tảng, đưa ra phương hướng và kế hoạch phù hợp với năng lực học sinh, giúp các em đặt mục tiêu trúng tuyển vào trường Chuyên, Chất lượng cao.'
                        ],
                        [
                            'image_url' => 'https://r2.nguoiviettainga.ru/1755271732.197.webp',
                            'title' => 'Khối trung học phổ thông',
                            'content' => 'Đánh giá năng lực, đưa ra định hướng và ôn luyện kiến thức cho học sinh bám sát theo cấu trúc đề thi THPT Quốc gia.'
                        ],
                        [
                            'image_url' => 'https://r2.nguoiviettainga.ru/1755271883.39.webp',
                            'title' => 'Luyện thi TSA',
                            'content' => 'Ôn luyện kiến thức cho học sinh bám sát cấu trúc đề thi Đánh giá tư duy, giúp các em đạt mục tiêu xét tuyển vào các trường đại học.'
                        ],
                    ],
                    'button_url' => 'https://clcntt.edu.vn/schedule'
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'homepage_review',
                'value' => json_encode([
                    'big_title_1' => 'Phụ huynh & học sinh nghĩ gì về lớp ôn',
                    'big_title_2' => 'Chất lượng cao Nguyễn Tất Thành',
                    'tiktok_urls' => [
                        ['url' => 'https://www.tiktok.com/@loponclc/video/7361469576527219976'],
                        ['url' => 'https://www.tiktok.com/@loponclc/video/7361067770454248722'],
                        ['url' => 'https://www.tiktok.com/@loponclc/video/7362889818461834514'],
                        ['url' => 'https://www.tiktok.com/@loponclc/video/7368830050512276754'],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'homepage_teachers',
                'value' => json_encode([
                    'big_title_1' => 'Đội ngũ giáo viên',
                    'big_title_2' => 'KINH NGHIỆM - TÀI NĂNG - TÂM HUYẾT',
                    'content' => [
                        [
                            'name' => 'Cô Nguyễn Quỳnh Trang',
                            'description' => 'Nơi công tác: trường THCS&THPT Nguyễn Tất Thành. Kinh nghiệm dạy học: Chuyên ôn luyện HSG khối 9,...'
                        ],
                        [
                            'name' => 'Thầy Lê Văn Cường',
                            'description' => 'Nơi công tác: Trường THCS&THPT Nguyễn Tất Thành. Thành tích cá nhân: Thạc sĩ chuyên ngành Toán.'
                        ],
                        [
                            'name' => 'Thầy Nguyễn Văn Tráng'
                        ],
                        [
                            'name' => 'Cô Đinh Lưu Hoàng Thái'
                        ]
                    ],
                    'button_url' => 'https://clcntt.edu.vn/teachers'
                ], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($data as $item) {
            DB::table('configs')->insert([
                'key' => $item['key'],
                'value' => $item['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
