<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
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
        return view('admin.laporan.simpanan', compact('simpanan'));
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
        return view('admin.laporan.pinjaman',compact('pinjaman'));
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
        $angsuran = $query->with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.laporan.angsuran', compact('angsuran'));
    }
}
