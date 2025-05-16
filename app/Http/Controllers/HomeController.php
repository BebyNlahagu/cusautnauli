<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $simpanan = Simpanan::sum('jumlah_simpanan');
        $pinjaman = Pinjaman::sum('jumlah_pinjaman');
        $angsuran = Anggsuran::sum('total_angsuran');
        $nasabah = User::count();
        $petugas = Petugas::first();
        $kapitalisasi = Pinjaman::sum('kapitalisasi');
        return view('home', compact('simpanan','pinjaman','angsuran','nasabah','kapitalisasi','petugas'));
    }
}
