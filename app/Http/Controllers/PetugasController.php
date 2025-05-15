<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        $prtugas = Petugas::with('user')->get();

        return view('admin.petugas.index');
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nama_lengkap' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'no_hp' => 'nullable|numeric',
            'img' => 'nullable',
        ]);

        $petugas = Petugas::findOrFail($id);
        if ($request->hasFile('img')) {
            $img = time(). '.' . $request->img->extension();
            $request->img->move(public_path('images'), $img);
        }

        $petugas->update([
            'user_id' => $request->user(),
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            // 'img' => 
        ]);

        return redirect()->back()->with('success','Berhasil Melakukan Update Data');
    }

}