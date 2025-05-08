<?php

use App\Http\Controllers\AnggsuranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SimpananController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::middleware(['auth', 'role:Admin'])->group(function () {

    Route::resource('/admin/nasabah', NasabahController::class);
    Route::resource('/admin/pinjaman', PinjamanController::class);
    Route::resource('/admin/angsuran', AnggsuranController::class);
    Route::resource('/admin/simpanan', SimpananController::class);

    Route::get('/get-pinjaman/{nasabah_id}', [AnggsuranController::class, 'getPinjaman']);

    Route::get('/admin/laporan/simpanan', [LaporanController::class, 'LaporanSimpanan'])->name('laporan.simpanan');
    Route::get('/admin/laporan/pinjaman', [LaporanController::class, 'LaporanPinjaman'])->name('laporan.pinjaman');
    Route::get('/admin/laporan/angsuran', [LaporanController::class, 'LaporanAngsuran'])->name('laporan.angsuran');

    Route::get('/admin/pdf/simpanan', [PdfController::class, 'simpananPdf'])->name('pdf.simpanan');
    Route::get('/admin/pdf/pinjaman', [PdfController::class, 'pinjamanPdf'])->name('pdf.pinjaman');
    Route::get('/admin/pdf/angsuran', [PdfController::class, 'angsuranPdf'])->name('pdf.angsuran');

    Route::post('/angsuran/update-status/{id}', [AnggsuranController::class, 'updateStatus'])->name('angsuran.updateStatus');
    Route::get('/get-max-pinjaman/{nasabah_id}', [PinjamanController::class, 'getMaxPinjaman'])->name('pinjaman.getMaxPinjaman');
    // Menambahkan route untuk memeriksa usia gabung nasabah
    Route::get('/check-nasabah-bergabung/{nasabah_id}', [NasabahController::class, 'checkNasabahBergabung']);
});

Route::middleware(['auth', 'role:Admin,kepala'])->prefix('admin')->group(function () {
    Route::prefix('laporan')->group(function () {
        Route::get('/simpanan', [LaporanController::class, 'LaporanSimpanan'])->name('laporan.simpanan');
        Route::get('/pinjaman', [LaporanController::class, 'LaporanPinjaman'])->name('laporan.pinjaman');
        Route::get('/angsuran', [LaporanController::class, 'LaporanAngsuran'])->name('laporan.angsuran');
    });

    Route::prefix('pdf')->group(function () {
        Route::get('/simpanan', [PdfController::class, 'simpananPdf'])->name('pdf.simpanan');
        Route::get('/pinjaman', [PdfController::class, 'pinjamanPdf'])->name('pdf.pinjaman');
        Route::get('/angsuran', [PdfController::class, 'angsuranPdf'])->name('pdf.angsuran');
    });
});


Route::middleware(['auth', 'role:User,Admin'])->prefix('admin')->group(function () {
    Route::resource('/admin/angsuran', AnggsuranController::class);
});
