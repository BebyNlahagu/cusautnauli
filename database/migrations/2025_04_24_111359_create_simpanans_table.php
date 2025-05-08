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
            $table->foreign("nasabah_id")->references('id')->on('nasabahs')->onDelete("cascade");
            $table->unsignedBigInteger("nasabah_id");
            $table->enum("jenis_simpanan",["Simpanan Pokok","Simpanan Wajib", "Simpanan Dakesma", "Biaya Administrasi"]);
            $table->decimal("jumlah_simpanan");
            $table->decimal("total" ,10 ,2)->nullable();
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
