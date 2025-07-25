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
        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreign("user_id")->references('id')->on('users')->onDelete("cascade");
            $table->unsignedBigInteger("user_id");
            $table->enum("jenis_simpanan",["Simpanan Pokok","Simpanan Wajib", "Simpanan Dakesma", "Biaya Administrasi"])->nullable();
            $table->decimal("jumlah_simpanan",15, 2);
            $table->decimal('jumlah_kapitalisasi',15,2)->nullable();
            $table->decimal("total" ,15 ,2)->nullable();
            $table->decimal("total_pinjaman" ,15 ,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanans');
    }
};
