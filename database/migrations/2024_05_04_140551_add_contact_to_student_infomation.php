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
        Schema::table('student_information', function (Blueprint $table) {
            $table->string('contact', 255)->index()->nullable();
            $table->string('external_id', 255)->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_information', function (Blueprint $table) {
            $table->dropColumn('contact');
            $table->dropColumn('external_id');

        });
    }
};
