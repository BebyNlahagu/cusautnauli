<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnggsuranController extends Controller
{
    public function index()
    {
        $angsuran = Anggsuran::with('nasabah', 'pinjaman')->get();
        $nasabah = Nasabah::all();
        return view("admin.angsuran.index", compact('angsuran', 'nasabah'));
    }

    public function getPinjaman($nasabah_id)
    {
        $pinjaman = Pinjaman::where('nasabah_id', $nasabah_id)->latest()->first();

        if (!$pinjaman) {
            return response()->json(['message' => 'Pinjaman tidak ditemukan.'], 404);
        }

        $lama = (int) filter_var($pinjaman->lama_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $jumlah = $pinjaman->jumlah_pinjaman;
        $bunga_persen = $pinjaman->bunga_pinjaman;

        // Bunga Flat
        $bunga_flat = $jumlah * ($bunga_persen / 100);
        $total_flat = $jumlah + $bunga_flat;
        $angsuran_flat = round($total_flat / $lama, 2);

        // Bunga Menurun (menggunakan sisa pokok)
        $sisa_pokok = $jumlah;
        $angsuran_pokok = $jumlah / $lama;
        $angsuran_bunga_menurun = [];

        for ($i = 0; $i < $lama; $i++) {
            $bunga_bulan_ini = ($sisa_pokok * ($bunga_persen / 100));
            $total_angsuran_bulan_ini = $angsuran_pokok + $bunga_bulan_ini;

            $angsuran_bunga_menurun[] = [
                'bulan_ke' => $i + 1,
                'sisa_pokok' => round($sisa_pokok, 2),
                'angsuran_pokok' => round($angsuran_pokok, 2),
                'bunga_bulan_ini' => round($bunga_bulan_ini, 2),
                'total_angsuran_bulan_ini' => round($total_angsuran_bulan_ini, 2),
            ];

            $sisa_pokok -= $angsuran_pokok;
        }

        return response()->json([
            'pinjaman_id' => $pinjaman->id,
            'jumlah_pinjaman' => $jumlah,
            'lama_pinjaman' => $pinjaman->lama_pinjaman,
            'kapitalisasi' => $pinjaman->kapitalisasi,
            'proposi' => $pinjaman->proposi,
            'terima_total' => $pinjaman->terima_total,
            'bunga_pinjaman' => $bunga_persen,
            'total_pinjaman_flat' => $total_flat,
            'angsuran_per_bulan_flat' => $angsuran_flat,
            'bunga_menurun' => $angsuran_bunga_menurun,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'pinjaman_id' => 'required|exists:pinjaman,id',
        ]);

        // Cari data pinjaman
        $pinjaman = Pinjaman::where('id', $request->pinjaman_id)
            ->where('nasabah_id', $request->nasabah_id)
            ->first();

        if (!$pinjaman) {
            return redirect()->back()->with('error', 'Pinjaman tidak valid untuk nasabah ini.');
        }

        $lama = (int) filter_var($pinjaman->lama_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $jumlah = $pinjaman->jumlah_pinjaman;
        $bunga_persen = $pinjaman->bunga_pinjaman;

        // Hitung pokok per bulan
        $pokok_per_bulan = $jumlah / $lama;
        $sisa_pokok = $jumlah;
        $sisa_angsuran_total = 0;

        $jatuh_tempo = Carbon::parse($request->jatuh_tempo);

        // Hitung dulu total semua angsuran untuk kebutuhan sisa angsuran
        $temp_sisa_pokok = $jumlah;
        $angsuran_per_bulan = [];

        for ($bulan = 1; $bulan <= $lama; $bulan++) {
            $bunga_bulan_ini = round($temp_sisa_pokok * ($bunga_persen / 100), 2);
            $total_bulan_ini = round($pokok_per_bulan + $bunga_bulan_ini, 2);
            $angsuran_per_bulan[$bulan] = $total_bulan_ini;

            $temp_sisa_pokok -= $pokok_per_bulan;
        }

        for ($bulan = 1; $bulan <= $lama; $bulan++) {
            $bunga_bulan_ini = round($sisa_pokok * ($bunga_persen / 100), 2);
            $total_angsuran = round($pokok_per_bulan + $bunga_bulan_ini, 2);

            $sisa_angsuran = array_sum(array_slice($angsuran_per_bulan, $bulan - 1));

            Anggsuran::create([
                'nasabah_id' => $request->nasabah_id,
                'pinjaman_id' => $request->pinjaman_id,
                'bulan_ke' => $bulan,
                'sisa_pokok' => $sisa_pokok,
                'angsuran_pokok' => $pokok_per_bulan,
                'bunga' => $bunga_bulan_ini,
                'total_angsuran' => $total_angsuran,
                'tanggal_jatuh_tempo' => $jatuh_tempo->copy()->addMonths($bulan - 1),
                'status' => 'Belum Lunas',
                'sisa_angsuran' => $sisa_angsuran, 
            ]);

            $sisa_pokok -= $pokok_per_bulan;
        }

        return redirect()->route('angsuran.index')->with('success', 'Jadwal Angsuran berhasil dibuat.');
    }

    public function updateStatus($id)
    {
        $angsuran = Anggsuran::findOrFail($id);

        $angsuran->status = 'Lunas';
        $angsuran->tanggal_bayar = now();
        $angsuran->save();

        return redirect()->back()->with('success', 'Status angsuran berhasil diubah menjadi lunas.');
    }
}
