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
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url', 5000);
            $table->text('listing_title');
            $table->text('listing_content');
            $table->string('listing_image', 5000);
            $table->timestamps();
        });

        Schema::create('video', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('video_url', 5000);
            $table->text('listing_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('video');
    }
};
