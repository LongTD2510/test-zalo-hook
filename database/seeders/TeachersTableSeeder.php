<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeachersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teachers')->insert([
            [
                'name' => 'Lê Thị Thanh Huyền',
                'age' => 30,
                'bullet_point' => json_encode([
                    'Experienced in Mathematics',
                    '10 years of teaching experience',
                    'Published several research papers'
                ]),
                'description' => 'Cô giáo Lê Thị Thanh Hương luôn khẳng định rằng giáo dục không chỉ đơn thuần là việc truyền đạt kiến thức mà còn là sứ mệnh cao cả của việc hình thành nhân cách và tạo ra những tương lai sáng sủa. Phương châm giảng dạy của cô là tạo ra một môi trường học tập cởi mở và đầy động lực, nơi mà mỗi học sinh được khuyến khích tự tin thể hiện bản thân, khám phá và phát triển tiềm năng toàn diện của mình. ',
                'motto' => 'Với niềm tin sâu đậm vào sức mạnh của giáo dục, cô luôn nỗ lực xây dựng một môi trường học tập tích cực và động viên học sinh không ngừng phát triển.',
                'quote' => 'Giáo dục là hành trình khám phá, nơi mỗi học sinh được khuyến khích vươn lên và phát triển không chỉ về mặt tri thức mà còn về tâm hồn. Đam mê là ngọn lửa dẫn lối, và vai trò của giáo viên là thổi bùng lên ngọn lửa đó để học sinh có thể chiến thắng mọi thách thức và đạt được ước mơ của mình.',
                'viewpoint' => 'Quan điểm giảng dạy của cô là tạo ra một không gian học tập thân thiện, nơi mà mỗi học sinh được khích lệ khám phá sự hứng thú và tài năng riêng của mình. ',
                'file_url' => 'assets/teachers/le_thi_thanh_huyen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyễn Thị Hà Phương',
                'age' => 28,
                'bullet_point' => json_encode([
                    'Specializes in Literature',
                    'Passionate about teaching',
                    'Organized several literature workshops'
                ]),
                'description' => 'Cô giáo Lê Thị Thanh Hương luôn khẳng định rằng giáo dục không chỉ đơn thuần là việc truyền đạt kiến thức mà còn là sứ mệnh cao cả của việc hình thành nhân cách và tạo ra những tương lai sáng sủa. Phương châm giảng dạy của cô là tạo ra một môi trường học tập cởi mở và đầy động lực, nơi mà mỗi học sinh được khuyến khích tự tin thể hiện bản thân, khám phá và phát triển tiềm năng toàn diện của mình. ',
                'motto' => 'Với niềm tin sâu đậm vào sức mạnh của giáo dục, cô luôn nỗ lực xây dựng một môi trường học tập tích cực và động viên học sinh không ngừng phát triển.',
                'quote' => 'Giáo dục là hành trình khám phá, nơi mỗi học sinh được khuyến khích vươn lên và phát triển không chỉ về mặt tri thức mà còn về tâm hồn. Đam mê là ngọn lửa dẫn lối, và vai trò của giáo viên là thổi bùng lên ngọn lửa đó để học sinh có thể chiến thắng mọi thách thức và đạt được ước mơ của mình.',
                'viewpoint' => 'Quan điểm giảng dạy của cô là tạo ra một không gian học tập thân thiện, nơi mà mỗi học sinh được khích lệ khám phá sự hứng thú và tài năng riêng của mình. ',
                'file_url' => 'assets/teachers/le_thi_thanh_huyen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trần Thị Thuý Loan',
                'age' => 35,
                'bullet_point' => json_encode([
                    'Expert in Physics',
                    '15 years of teaching experience',
                    'Author of several physics textbooks'
                ]),
                'description' => 'Cô giáo Lê Thị Thanh Hương luôn khẳng định rằng giáo dục không chỉ đơn thuần là việc truyền đạt kiến thức mà còn là sứ mệnh cao cả của việc hình thành nhân cách và tạo ra những tương lai sáng sủa. Phương châm giảng dạy của cô là tạo ra một môi trường học tập cởi mở và đầy động lực, nơi mà mỗi học sinh được khuyến khích tự tin thể hiện bản thân, khám phá và phát triển tiềm năng toàn diện của mình. ',
                'motto' => 'Với niềm tin sâu đậm vào sức mạnh của giáo dục, cô luôn nỗ lực xây dựng một môi trường học tập tích cực và động viên học sinh không ngừng phát triển.',
                'quote' => 'Giáo dục là hành trình khám phá, nơi mỗi học sinh được khuyến khích vươn lên và phát triển không chỉ về mặt tri thức mà còn về tâm hồn. Đam mê là ngọn lửa dẫn lối, và vai trò của giáo viên là thổi bùng lên ngọn lửa đó để học sinh có thể chiến thắng mọi thách thức và đạt được ước mơ của mình.',
                'viewpoint' => 'Quan điểm giảng dạy của cô là tạo ra một không gian học tập thân thiện, nơi mà mỗi học sinh được khích lệ khám phá sự hứng thú và tài năng riêng của mình. ',
                'file_url' => 'assets/teachers/le_thi_thanh_huyen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vũ Thị Ngọc Minh',
                'age' => 40,
                'bullet_point' => json_encode([
                    'Specializes in Chemistry',
                    '20 years of teaching experience',
                    'Conducted numerous chemistry experiments'
                ]),
                'description' => 'Cô giáo Lê Thị Thanh Hương luôn khẳng định rằng giáo dục không chỉ đơn thuần là việc truyền đạt kiến thức mà còn là sứ mệnh cao cả của việc hình thành nhân cách và tạo ra những tương lai sáng sủa. Phương châm giảng dạy của cô là tạo ra một môi trường học tập cởi mở và đầy động lực, nơi mà mỗi học sinh được khuyến khích tự tin thể hiện bản thân, khám phá và phát triển tiềm năng toàn diện của mình. ',
                'motto' => 'Với niềm tin sâu đậm vào sức mạnh của giáo dục, cô luôn nỗ lực xây dựng một môi trường học tập tích cực và động viên học sinh không ngừng phát triển.',
                'quote' => 'Giáo dục là hành trình khám phá, nơi mỗi học sinh được khuyến khích vươn lên và phát triển không chỉ về mặt tri thức mà còn về tâm hồn. Đam mê là ngọn lửa dẫn lối, và vai trò của giáo viên là thổi bùng lên ngọn lửa đó để học sinh có thể chiến thắng mọi thách thức và đạt được ước mơ của mình.',
                'viewpoint' => 'Quan điểm giảng dạy của cô là tạo ra một không gian học tập thân thiện, nơi mà mỗi học sinh được khích lệ khám phá sự hứng thú và tài năng riêng của mình. ',
                'file_url' => 'assets/teachers/le_thi_thanh_huyen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
