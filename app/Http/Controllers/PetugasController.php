<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::where('user_id', auth()->id())->first();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nama_lengkap' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'no_hp' => 'nullable|numeric',
            'img' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
        ];

        if ($request->hasFile('img')) {
            $img = time() . '.' . $request->img->extension();
            $request->img->move(public_path('images'), $img);
            $data['img'] = $img;
        }

        Petugas::create($data);

        return redirect()->route('petugas.index')->with('success', 'Berhasil Menambahkan Data');
    }


    public function edit($id)
    {
        $petugas = Petugas::where('user_id', auth()->id())->first();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nama_lengkap' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'no_hp' => 'nullable|numeric',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|',
        ]);

        $petugas = Petugas::findOrFail($id);

        $updateData = [
            'user_id' => auth()->id(),
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
        ];

        if ($request->hasFile('img')) {
            $img = time() . '.' . $request->img->extension();
            $request->img->move(public_path('images'), $img);
            $updateData['img'] = $img;
        }

        $petugas->update($updateData);

        return redirect()->route('petugas.index')->with('success', 'Berhasil Melakukan Update Data');
    }
}
