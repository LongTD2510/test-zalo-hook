<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_information', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // Số Báo Danh
            $table->string('full_name');  // Họ Tên
            $table->integer('exam_school_year_id'); // Mã Kỳ Thi
            $table->string('room');  // Phòng Thi
            $table->string('location'); // Địa Điểm Thi
            $table->decimal('math', 5, 2); // Điểm Toán
            $table->decimal('english', 5, 2); // Điểm Anh
            $table->decimal('literature', 5, 2); // Điểm Văn
            $table->timestamps();
            $table->softDeletes();

            // Đánh index cho các trường
            $table->index('student_id');
            $table->index('full_name');
            $table->index('exam_school_year_id');
            $table->index('room');
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_information');
    }
};
