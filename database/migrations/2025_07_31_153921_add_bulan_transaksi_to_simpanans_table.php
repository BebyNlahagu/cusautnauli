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
            $table->date('bulan_transaksi')->nullable()->after('jumlah_simpanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simpanans', function (Blueprint $table) {
            $table->dropColumn("bulan_transaksi");
        });
    }
};
