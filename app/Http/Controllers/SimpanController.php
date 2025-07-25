<?php

namespace App\Http\Controllers;

use App\Models\Simpan;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Http\Request;

class SimpanController extends Controller
{
    public function getUserSimpans($id)
    {
        $user = User::with('simpanan')->findOrFail($id);

        return response()->json([
            'user' => $user->name,
            'simpanan' => $user->simpans->map(function ($simpan) {
                return [
                    'nama_simpanan' => $simpan->nama_simpanan,
                    'besar_simpanan' => number_format($simpan->besar_simpanan, 0, ',', '.'),
                    'tanggal' => \Carbon\Carbon::parse($simpan->created_at)->translatedFormat('d F Y'),
                ];
            }),
        ]);
    }
}
