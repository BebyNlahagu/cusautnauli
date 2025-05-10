<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function simpananPdf(Request $request)
    {
        $simpanan = Simpanan::with('nasabah')->get();

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
        $user = Auth::user();
        $pdf = Pdf::loadView('admin.pdf.simpananPdf', [
            'simpanan' => $simpanan,
            'user' => $user
        ]);
        return $pdf->download('Laporan-Simpanan.pdf');
    }

    public function pinjamanPdf(Request $request)
    {
        $pinjaman = Pinjaman::with('nasabah')->get();
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
        $user = Auth::user();
        $pdf = Pdf::loadView('admin.pdf.pinjamanPdf', [
            'pinjaman' => $pinjaman,
            'user' => $user
        ]);
        return $pdf->download('Laporan-Pinjaman.pdf');
    }

    public function angsuranPdf(Request $request)
    {
        $angsuran = Anggsuran::with('nasabah','pinjaman')->get();
        $query = Anggsuran::query();
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_main', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_main', $request->tahun);
        }
        if ($request->filled('hari')) {
            $query->whereDate('tanggal_main', $request->hari);
        }
        $user = Auth::user();
        $pdf = Pdf::loadView('admin.pdf.angsuranPdf', [
            'angsuran' => $angsuran,
            'user' => $user
        ]);
        return $pdf->download('Laporan-Angsuran.pdf');
    }
}
