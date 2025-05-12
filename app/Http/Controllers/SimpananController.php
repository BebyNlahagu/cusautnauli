<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $simpanan = Simpanan::with('nasabah')->get();
        $nasabah = Nasabah::all();
        $kapitalisasi = Simpanan::sum('jumlah_kapitalisasi');
        return view("admin.simpanan.index", compact('simpanan', 'nasabah','kapitalisasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        $simpanan = Simpanan::where('nasabah_id', $request->nasabah_id)
            ->where('jenis_simpanan', $request->jenis_simpanan)
            ->first();

        if ($simpanan) {
            $simpanan->jumlah_simpanan += $jumlahSetelahPotong;
            $simpanan->jumlah_kapitalisasi += $potongan;
            $simpanan->save();
        } else {
            $simpananAll = $jumlahSetelahPotong;
            $kap = $potongan;

            Simpanan::create([
                'nasabah_id' => $request->nasabah_id,
                'jumlah_simpanan' => $simpananAll,
                'jumlah_kapitalisasi' => $kap,
                'jenis_simpanan' => $request->jenis_simpanan,
            ]);
        }

        return redirect()->route('simpanan.index')->with('success', 'Data Berhasil Di Tambahkan');
    }


    public function edit($id)
    {
        $simpanan = Simpanan::findOrFail($id)->get();
        return redirect()->route('simpanan.index', compact('simpanan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nasabah_id' => 'nullable|exists:nasabahs,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $simpanan = Simpanan::findOrFail($id);

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        $simpanan = Simpanan::where('nasabah_id', $request->nasabah_id)
            ->where('jenis_simpanan', $request->jenis_simpanan)
            ->first();

        if ($simpanan) {
            $simpanan->jumlah_simpanan += $jumlahSetelahPotong;
            $simpanan->jumlah_kapitalisasi += $potongan;
            $simpanan->save();
        } else {
            Simpanan::create([
                'nasabah_id' => $request->nasabah_id,
                'jumlah_simpanan' => $jumlahSetelahPotong,
                'jumlah_kapitalisasi' => $potongan,
                'jenis_simpanan' => $request->jenis_simpanan,
            ]);
        }


        return redirect()->route('simpanan.index')->with('success', 'Data Berhasil Di Perbarui');
    }

    public function destroy($id)
    {
        Simpanan::findorfail($id)->delete();
        return redirect()->route('simpanan.index')->with('delete', 'Data Berhasil Dihapus');
    }
}
