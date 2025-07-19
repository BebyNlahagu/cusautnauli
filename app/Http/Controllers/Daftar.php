<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\User;
use Illuminate\Http\Request;

class Daftar extends Controller
{
    public function index()
    {
        $alamat = Alamat::all();
        return view("landingpage",compact("alamat"));
    }

    public function edit()
    {
        $user = auth()->user();
        $alamat = Alamat::all();
        return view('profil', compact('user','alamat'));
    }

   public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'Nik' => 'nullable',
            'no_telp' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'tanggal_lahir' => 'nullable',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kecamatan' => 'nullable',
            'desa' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'nullable',
        ]);

        $nasabah = User::findOrFail($id);
        if ($request->hasFile('foto')) {
            if ($nasabah->foto && file_exists(public_path('images/' . $nasabah->foto))) {
                unlink(public_path('images/' . $nasabah->foto));
            }
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('images'), $foto);
        } else {
            $foto = $nasabah->foto;
        }

        if ($request->hasFile('ktp')) {
            if ($nasabah->ktp && file_exists(public_path('images/' . $nasabah->ktp))) {
                unlink(public_path('images/' . $nasabah->ktp));
            }
            $ktp = time() . '.' . $request->ktp->extension();
            $request->ktp->move(public_path('images'), $ktp);
        } else {
            $ktp = $nasabah->ktp;
        }

        if ($request->hasFile('kk')) {
            if ($nasabah->kk && file_exists(public_path('images/' . $nasabah->kk))) {
                unlink(public_path('images/' . $nasabah->kk));
            }
            $kk = time() . '.' . $request->kk->extension();
            $request->kk->move(public_path('images'), $kk);
        } else {
            $kk = $nasabah->kk;
        }

        $nasabah->update([
            'name' => $request->filled('name') ? $request->name : $nasabah->name,
            'Nik' => $request->filled('nik') ? $request->Nik : $nasabah->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->filled('jenis_kelamin') ? $request->jenis_kelamin : $nasabah->jenis_kelamin,
            'tanggal_lahir' => $request->filled('tanggal_lahir') ? $request->tanggal_lahir : $nasabah->tanggal_lahir,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
            'foto' => $foto,
            'ktp' => $ktp,
            'kk' => $kk,
        ]);

        return redirect()->route('user.edit')->with('success', 'Data berhasil diPerbarui!');
    }
}
