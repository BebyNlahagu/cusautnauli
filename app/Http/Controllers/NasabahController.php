<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function index()
    {
        $nasabah = Nasabah::all();
        return view('admin.nasabah.index', compact('nasabah'));
    }

    // public function checkNasabahBergabung($nasabah_id)
    // {
    //     try {
    //         $nasabah = Nasabah::findOrFail($nasabah_id);
    //         $bergabung_sejak = Carbon::parse($nasabah->tanggal_masuk);
    //         $sekarang = now();
    //         $selisih_bulan = $bergabung_sejak->diffInMonths($sekarang);

    //         return response()->json(['selisih_bulan' => $selisih_bulan]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Nasabah tidak ditemukan.'], 404);
    //     }
    // }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'Nik' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'foto' => 'nullable',
            'ktp' => 'nullable',
            'kk' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'required'
        ]);

        if ($request->hasFile('foto')) {
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('images'), $foto);
        }

        if ($request->hasFile('ktp')) {
            $ktp = time() . '.' . $request->ktp->extension();
            $request->ktp->move(public_path('images'), $ktp);
        }

        if ($request->hasFile('kk')) {
            $kk = time() . '.' . $request->kk->extension();
            $request->kk->move(public_path('images'), $kk);
        }

        Nasabah::create([
            'name' => $request->name,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tanggal_masuk' => $request->tanggal_masuk,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
        ]);

        return redirect()->route('nasabah.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $nasabah = Nasabah::findOrfail($id)->get();
        return redirect()->route('nasabah.index', compact('nasabah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable',
            'Nik' => 'nullable',
            'no_telp' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'tanggal_lahir' => 'nullable',
            'foto' => 'nullable',
            'kk' => 'nullable',
            'ktp' => 'nullable',
            'tanggal_masuk' => 'nullable',
            'alamat' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'nullable',
            'foto' => isset($foto) ? $foto : null,
            'ktp' => isset($ktp) ? $ktp : null,
            'kk' => isset($kk) ? $kk : null
        ]);

        $nasabah = Nasabah::findOrFail($id);

      
        if ($request->hasFile('foto')) {
            if ($nasabah->foto) {
                unlink(public_path('images/' . $nasabah->foto));
            }

            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('images'), $foto);
        } else {
            $foto = $nasabah->foto;
        }

        if ($request->hasFile('ktp')) {
            if ($nasabah->ktp) {
                unlink(public_path('images/' . $nasabah->ktp));
            }

            $ktp = time() . '.' . $request->ktp->extension();
            $request->ktp->move(public_path('images'), $ktp);
        } else {
            $ktp = $nasabah->ktp;
        }

        if ($request->hasFile('kk')) {
            if ($nasabah->kk) {
                unlink(public_path('images/' . $nasabah->kk));
            }

            $kk = time() . '.' . $request->kk->extension();
            $request->kk->move(public_path('images'), $kk);
        } else {
            $kk = $nasabah->kk;
        }

        $nasabah->update([
            'name' => $request->name,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
            'foto' => isset($foto) ? $foto : $nasabah->foto, 
            'ktp' => isset($ktp) ? $ktp : $nasabah->ktp, 
            'kk' => isset($kk) ? $kk : $nasabah->kk,
        ]);

        return redirect()->route('nasabah.index')->with('success', 'Data berhasil diPerbarui!');
    }

    public function destroy($id)
    {
        Nasabah::findOrFail($id)->delete();

        return redirect()->route('nasabah.index')->with('delete', 'Data berhasil diHapus!');
    }
}
