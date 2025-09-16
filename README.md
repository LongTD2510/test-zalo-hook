Dự án Web Giáo Dục.

Yêu cầu môi trường:
PHP 8.1
Laravel 10
Mysql
Docker

Các bước deploy cho dev: 
1. pull git
2. Chạy docker-compose -f docker-compose.yaml up -d --build
3. Chạy docker exec -it team_klasse_be-app-1 sh
4. chạy composer install trong docker container
5. chạy php artisan jwt:secret

Import local db data
https://drive.google.com/file/d/1zaaij83zAH0-EExQE16CZ1a5gpwxm7bN/view?usp=drive_link


Yêu cầu code: 
1. Sử dụng 'use App\Traits\ApiResponse;', 'use ApiResponse;' trong tất cả các controller, response sử dụng $this->errorResponse() hoặc $this->successResponse() để chuẩn hóa response.

2. logic phức tạp thì sử dụng service, không viết logic nặng ở controller, logic validate nặng thì sử dụng request và rule
