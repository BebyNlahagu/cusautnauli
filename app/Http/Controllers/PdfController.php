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
    public function simpananPdf()
    {
        $simpanan = Simpanan::with('nasabah')->get();
        $user = Auth::user();
        $pdf = Pdf::loadView('admin.pdf.simpananPdf', [
            'simpanan' => $simpanan,
            'user' => $user
        ]);
        return $pdf->download('Laporan-Simpanan.pdf');
    }

    public function pinjamanPdf()
    {
        $pinjaman = Pinjaman::with('nasabah')->get();
        $user = Auth::user();
        $pdf = Pdf::loadView('admin.pdf.pinjaman.Pdf', [
            'pinjaman' => $pinjaman,
            'user' => $user
        ]);
        return $pdf->download('Laporan-Pinjaman.pdf');
    }

    public function angsuranPdf()
    {
        $angsuran = Anggsuran::with('nasabah','pinjaman')->get();
        $pdf = Pdf::loadView('admin.pdf.angsuranPdf', ['angsuran' => $angsuran]);
        return $pdf->download('Laporan-Angsuran.pdf');
    }
}
