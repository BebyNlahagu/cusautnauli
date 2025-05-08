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
        $simpanan = Simpanan::with('nasabah')->get();
        return view('admin.laporan.simpanan', compact('simpanan'));
    }

    public function LaporanPinjaman(Request $request)
    {
        $pinjaman = Pinjaman::with('nasabah')->get();
        return view('admin.laporan.pinjaman',compact('pinjaman'));
    }

    public function LaporanAngsuran(Request $request)
    {
        $angsuran = Anggsuran::with('nasabah','pinjaman')->get();
        return view('admin.laporan.angsuran', compact('angsuran'));
    }
}
