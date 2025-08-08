<?php

use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/simpanan/{userId}', function($userId) {
    $simpanan = Simpanan::where('user_id', $userId)->get();

    $data = $simpanan->map(function($item) {
        return [
            'id' => $item->id,
            'jenis_simpanan' => $item->jenis_simpanan,
            'jumlah_simpanan' => $item->jumlah_simpanan,
            'tanggal' => $item->created_at->format('Y-m-d'),
            'status' => $item->status
        ];
    });

    return response()->json(['simpanan' => $data]);
});
