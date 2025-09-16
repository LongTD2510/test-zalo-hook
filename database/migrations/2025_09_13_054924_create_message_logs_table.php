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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ZNS', 'OA', 'CUSTOM'])->index();
            $table->string('phone')->nullable();
            $table->string('user_id')->nullable();
            $table->string('template_id')->nullable();
            $table->string('template_name')->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('status')->default('PENDING');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
