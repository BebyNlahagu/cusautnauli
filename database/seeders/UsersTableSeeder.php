<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administator',
            'username' => 'admin',
            'nm_koperasi' => null,
            'email' => 'ahyani@example.com',
            'email_verified_at' => now(),
            'role' => 'Admin',
            'password' => Hash::make('12345678'),
            'Nik' => '3201123456789012',
            'nmr_anggota' => null,
            'no_telp' => '081234567890',
            'kelurahan' => 'null',
            'foto' => 'default.jpg',
            'ktp' => 'ktp.jpg',
            'kk' => 'kk.jpg',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-01-01',
            'pekerjaan' => 'null',
            'kecamatan' => 'Kecamatan Lintong Nihuta',
            'desa' => 'Desa Nagasaribu I',
            'status' => 'Verify',
            'simpanan_wajib' => true,
            'administrasi' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
