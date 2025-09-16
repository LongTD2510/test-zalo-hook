<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            // MySQL cho phép đổi text -> json
            $table->json('value')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            // Rollback về text
            $table->text('value')->nullable()->change();
        });
    }
};
