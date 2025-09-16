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
            $table->string('room', 255)->nullable()->change();
            $table->string('location', 255)->nullable()->change();
            $table->float('math')->nullable()->change();
            $table->float('english')->nullable()->change();
            $table->float('literature')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
