<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        $prtugas = Petugas::with('user')->get();

        return view('admin.petugas.index');
    }

    public function petugas(Request $request)
    {

    }

}