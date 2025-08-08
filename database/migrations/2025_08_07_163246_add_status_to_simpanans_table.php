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
        Schema::table('simpanans', function (Blueprint $table) {
            $table->enum('status',["Belum Lunas", "Lunas"])->default('Belum Lunas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simpanans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
