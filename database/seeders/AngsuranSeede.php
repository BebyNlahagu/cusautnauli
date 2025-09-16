<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AngsuranSeede extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('angsuran')->insert([
            [
                'pinjaman_id' => 1,
                'user_id' => 1,
                'bulan_ke' => 1,
                'sisa_pokok' => 1000000,
                'sisa_angsuran' => 1100000,
                'angsuran_pokok' => 100000,
                'bunga' => 10000,
                'total_angsuran' => 110000,
                'jumlah_bayar' => 110000,
                'total_pinjaman' => 1000000,
                'tanggal_jatuh_tempo' => Carbon::now()->addMonth(),
                'tanggal_bayar' => Carbon::now(),
                'status' => 'Lunas',
                'denda' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'pinjaman_id' => 1,
                'user_id' => 1,
                'bulan_ke' => 2,
                'sisa_pokok' => 900000,
                'sisa_angsuran' => 990000,
                'angsuran_pokok' => 100000,
                'bunga' => 9000,
                'total_angsuran' => 109000,
                'jumlah_bayar' => null,
                'total_pinjaman' => 1000000,
                'tanggal_jatuh_tempo' => Carbon::now()->addMonths(2),
                'tanggal_bayar' => null,
                'status' => 'Belum Lunas',
                'denda' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
