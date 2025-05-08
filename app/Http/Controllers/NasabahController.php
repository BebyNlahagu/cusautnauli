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

    // Fungsi untuk memeriksa apakah nasabah sudah bergabung lebih dari 6 bulan
    public function checkNasabahBergabung($nasabah_id)
    {
        try {
            $nasabah = Nasabah::findOrFail($nasabah_id);
            $bergabung_sejak = Carbon::parse($nasabah->tanggal_masuk); // parse ke Carbon
            $sekarang = now();
            $selisih_bulan = $bergabung_sejak->diffInMonths($sekarang);
        
            return response()->json(['selisih_bulan' => $selisih_bulan]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nasabah tidak ditemukan.'], 404);
        }
    }


    public function store(Request $request)
    {
        // dd($request->tanggal_lahir);
        $request->validate([
            'name' => 'required',
            'Nik' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'foto' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'required'
        ]);


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
            'tanggal_masuk' => 'nullable',
            'alamat' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'nullable'
        ]);

        $nasabah = Nasabah::findOrFail($id);

        $nasabah->update([
            'name' => $request->name,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
        ]);

        return redirect()->route('nasabah.index')->with('success', 'Data berhasil diPerbarui!');
    }

    public function destroy($id)
    {
        Nasabah::findOrFail($id)->delete();

        return redirect()->route('nasabah.index')->with('delete', 'Data berhasil diHapus!');
    }
}
