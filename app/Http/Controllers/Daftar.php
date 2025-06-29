<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;

class Daftar extends Controller
{
    public function index()
    {
        $alamat = Alamat::all();
        return view("landingpage",compact("alamat"));
    }
}
