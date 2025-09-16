<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_school_year', function (Blueprint $table) {
            $table->id();
            $table->integer('examination_id')->index('examination_id');
            $table->integer('school_year_id')->index('school_year_id');
            $table->unique(['examination_id', 'school_year_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_school_year');
    }
};
