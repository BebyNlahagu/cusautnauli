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
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id();
            $table->foreign('pinjaman_id')->references('id')->on('pinjaman')->onDelete('cascade');
            $table->unsignedBigInteger('pinjaman_id');
            $table->foreign('user_id')->references('id')->on('nasabahs')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');

            $table->integer('bulan_ke');
            $table->decimal('sisa_pokok', 15, 2);
            $table->decimal('sisa_angsuran',15,2);
            $table->decimal('angsuran_pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('total_angsuran', 15, 2);
            $table->decimal('jumlah_bayar', 15, 2)->nullable();
            $table->decimal('total_pinjaman',15,2)->nullable();
            
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->decimal('denda',15,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran');
    }
};
