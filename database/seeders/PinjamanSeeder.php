<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PinjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lamaPinjamanOptions = ["5 Bulan", "10 Bulan", "15 Bulan", "20 Bulan", "25 Bulan", "30 Bulan"];

        // Ambil semua nasabah
        $nasabahs = Nasabah::all();

        foreach ($nasabahs as $nasabah) {
            // Misal: Tidak semua nasabah meminjam, hanya 50% dari nasabah
            if (rand(0, 1)) {
                $lamaPinjaman = $lamaPinjamanOptions[array_rand($lamaPinjamanOptions)];
                $jumlahPinjaman = rand(1000000, 10000000); // 1 juta sampai 10 juta
                $bungaPinjaman = 3; // Tetap 3%

                // Kapitalisasi, proposi, terima total dihitung
                $kapitalisasi = $jumlahPinjaman * ($bungaPinjaman / 100);
                $proposi = $jumlahPinjaman / (int) filter_var($lamaPinjaman, FILTER_SANITIZE_NUMBER_INT); // Angsuran pokok per bulan
                $terimaTotal = $jumlahPinjaman - $kapitalisasi; // Total uang diterima nasabah

                DB::table('pinjaman')->insert([
                    'nasabah_id' => $nasabah->id,
                    'lama_pinjaman' => $lamaPinjaman,
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'kapitalisasi' => $kapitalisasi,
                    'proposi' => $proposi,
                    'bunga_pinjaman' => $bungaPinjaman,
                    'terima_total' => $terimaTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
