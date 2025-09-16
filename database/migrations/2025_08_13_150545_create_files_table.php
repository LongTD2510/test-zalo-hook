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
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id'); // bigint unsigned auto_increment primary key
            $table->string('file_url', 255)->nullable();
            $table->string('type', 255);
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('sync_status', 255)->default('pending');
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at

            // Nếu muốn tăng tốc truy vấn lọc theo type + type_id
            $table->index(['type', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
