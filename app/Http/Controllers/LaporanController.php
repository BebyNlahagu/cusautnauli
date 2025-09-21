<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function LaporanSimpanan(Request $request)
    {
        $query = Simpanan::query();
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        if ($request->filled('hari')) {
            $query->whereDate('created_at', $request->hari);
        }
        $simpanan = $query->with('user')->orderBy('created_at', 'desc')->get();

        $simpanan->each(function ($item) {
            $item->tanggal_terakhir = $item->created_at->translatedFormat('F Y');
        });

        $groupedSimpanan = $simpanan->groupBy('user_id')->map(function ($items) {
            return [
                'user' => $items->first()->user,
                'total_simpanan' => $items->sum('jumlah_simpanan'),
                'total_kapitalisasi' => $items->sum('jumlah_kapitalisasi'),
                'tanggal_terakhir' => $items->max('created_at')->translatedFormat('F Y'),
                'jumlah_transaksi' => $items->count(),
            ];
        });

        $jumlah = Simpanan::sum('jumlah_simpanan');

        return view('admin.laporan.simpanan', compact('simpanan', 'groupedSimpanan','jumlah'));
    }

    public function LaporanPinjaman(Request $request)
    {
        $query = Pinjaman::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        if ($request->filled('hari')) {
            $query->whereDate('created_at', $request->hari);
        }

        $pinjaman = $query->with('user')->orderBy('created_at', 'desc')->get();

        $pinjaman_terakhir = $pinjaman->groupBy('user_id')->map(function ($items) {
            return $items->first();
        })->values();

        return view('admin.laporan.pinjaman', [
            'pinjaman' => $pinjaman_terakhir,
            'semua_pinjaman' => $pinjaman,
        ]);
    }


    public function LaporanAngsuran(Request $request)
    {
        $query = Anggsuran::query();
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        if ($request->filled('hari')) {
            $query->whereDate('created_at', $request->hari);
        }
        $jumlahAngsuran = Anggsuran::sum("total_angsuran");
        $angsuran = $query->with(['user', 'pinjaman'])->orderBy('created_at', 'desc')->get();
        return view('admin.laporan.angsuran', compact('angsuran','jumlahAngsuran'));
    }

    public function LaporanAnggota(Request $request)
    {
        $query = User::query();
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        if ($request->filled('hari')) {
            $query->whereDate('created_at', $request->hari);
        }
        $user = $query->orderBy('nm_koperasi', 'asc')->get();
        return view('admin.laporan.anggota', compact('user'));
    }
}
