<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisSimpanan = ["Simpanan Pokok", "Simpanan Wajib", "Simpanan Dakesma", "Biaya Administrasi"];

        // Ambil semua nasabah
        $nasabahs = Nasabah::all();

        foreach ($nasabahs as $nasabah) {
            // Misal: setiap nasabah akan punya 1-3 data simpanan
            $jumlahData = rand(1, 3);

            for ($i = 0; $i < $jumlahData; $i++) {
                $jumlahSimpanan = rand(100000, 500000); // Random jumlah antara 100rb - 500rb

                DB::table('simpanans')->insert([
                    'nasabah_id' => $nasabah->id,
                    'jenis_simpanan' => $jenisSimpanan[array_rand($jenisSimpanan)],
                    'jumlah_simpanan' => $jumlahSimpanan,
                    'total' => $jumlahSimpanan + rand(1000, 10000), // total bisa beda sedikit
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
