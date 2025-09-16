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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('code')->unique(); 
            $table->enum('channel', ['ZNS', 'CUSTOM_OA'])->nullable();
            $table->string('template_id')->nullable();
            $table->string('name')->nullable();  
            $table->string('status')->nullable();
            $table->string('template_quality')->nullable();
            $table->text('content')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('thumb_url', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('template_tag')->nullable();
            $table->string('price')->nullable();
            $table->json('params')->nullable();
            $table->boolean('is_welcome')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
