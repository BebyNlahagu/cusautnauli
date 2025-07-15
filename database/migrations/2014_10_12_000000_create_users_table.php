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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("username")->unique();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role',['Admin', 'Kepala', 'User'])->default("User");
            $table->string('password');

            #Data Nasabah
            $table->string('Nik')->nullable();
            $table->string('nmr_anggota')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('foto')->nullable();
            $table->string('ktp')->nullable();
            $table->string('kk')->nullable();
            $table->enum('jenis_kelamin',['Laki-laki','Perempuan']);
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->enum('status',['Verify', 'Unverifyed'])->default('Unverifyed');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
