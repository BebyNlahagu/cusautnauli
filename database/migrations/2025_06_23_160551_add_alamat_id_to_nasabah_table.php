<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nasabahs', function (Blueprint $table) {
            $table->unsignedBigInteger('alamat_id')->nullable()->after('id');
            $table->foreign('alamat_id')->references('id')->on('alamats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabahs', function (Blueprint $table) {
             $table->dropForeign(['alamat_id']);
            $table->dropColumn('alamat_id');
        });
    }
};
