<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Simpan;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpananController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == "Admin") {
            $simpanan = Simpanan::with('user')->get();
            $nasabah = User::where('status', 'Verify')->get();
            $kapitalisasi = Simpanan::sum('jumlah_kapitalisasi');
        } else {
            $simpanan = Simpanan::with('user')->where('user_id', $user->user_id)->get();
            $nasabah = User::where('id', $user->user_id)->get();
            $kapitalisasi = Simpanan::where('user_id', $user->user_id)->sum('jumlah_kapitalisasi');
        }
        return view("admin.simpanan.index", compact('simpanan', 'nasabah', 'kapitalisasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        $simpanan = Simpanan::where('user_id', $request->user_id)
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
                'user_id' => $request->user_id,
                'jumlah_simpanan' => $simpananAll,
                'jumlah_kapitalisasi' => $kap,
                'jenis_simpanan' => $request->jenis_simpanan,
            ]);
        }

        Simpan::create([
            'user_id' => $request->user_id,
            'nama_simpanan' => $request->jenis_simpanan ?? 'Tidak diketahui',
            'besar_simpanan' => $jumlahSetelahPotong,
        ]);


        return redirect()->route('simpanan.index')->with('success', 'Data Berhasil Di Tambahkan');
    }


    public function edit($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        return redirect()->route('simpanan.index', compact('simpanan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $simpanan = Simpanan::findOrFail($id);

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        $simpanan = Simpanan::where('user_id', $request->user_id)
            ->where('jenis_simpanan', $request->jenis_simpanan)
            ->first();

        if ($simpanan) {
            $simpanan->jumlah_simpanan += $jumlahSetelahPotong;
            $simpanan->jumlah_kapitalisasi += $potongan;
            $simpanan->save();
        } else {
            Simpanan::create([
                'user_id' => $request->user_id,
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
