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
        Schema::create('notify_month_quarter', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('week', 255)->nullable();
            $table->string('month', 255)->nullable();
            $table->string('year', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('month_quarter_id');
            $table->bigInteger('grade_id');
            $table->string('name', 255)->nullable();
            $table->string('contact', 255)->nullable();
            $table->string('contact_2', 255)->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notify_month_quarter');
        Schema::dropIfExists('notifications');
    }
};
