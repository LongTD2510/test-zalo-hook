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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Lưu key (ví dụ: site_name, timezone)
            $table->text('value')->nullable(); // Lưu value (có thể là chuỗi dài)
            $table->timestamps();
            $table->softDeletes(); // Thêm cột deleted_at để hỗ trợ soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
