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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('lama_pinjaman',["6 Bulan","12 Bulan", "18 Bulan", "24 Bulan", "30 Bulan", "36 Bulan"]);
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('kapitalisasi',15,2)->nullable();
            $table->decimal('proposi',15,2)->nullable();
            $table->integer('bunga_pinjaman');
            $table->decimal('terima_total',15,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
