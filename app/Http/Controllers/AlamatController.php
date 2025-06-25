<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    public function index()
    {
        $alamat = Alamat::all();
        return view('alamat',compact('alamat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "alamat" => "required",
        ]);

        Alamat::create([
            "alamat" => $request->alamat,
        ]);

        return redirect()->back()->with("success","Data Berhasil Ditambahkan");
    }
}
