<?php

use App\Http\Controllers\AlamatController;
use App\Http\Controllers\AnggsuranController;
use App\Http\Controllers\Daftar;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PetugasController;
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
    return view('landingPage');
});

Auth::routes();

Route::post('/notifications/{id}/mark-as-read', function ($id) {
    $notification = auth()->user()->unreadNotifications()->findOrFail($id);
    $notification->markAsRead();
    return response()->json(['status' => 'success']);
})->name('notifications.read');


Route::get('/alamat', [AlamatController::class,"index"])->name("alamat.index");
Route::post('/alamat',[AlamatController::class, "store"])->name("alamat.store");

Route::get('/nasabah',[NasabahController::class,'create'])->name("create");
Route::post('/nasabah',[NasabahController::class, 'addData'])->name('addNasabah');
Route::get('/',[Daftar::class,'index']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::middleware(['auth', 'role:Admin'])->group(function () {

    Route::resource('/admin/nasabah', NasabahController::class);
    Route::resource('/admin/pinjaman', PinjamanController::class);
    Route::resource('/admin/angsuran', AnggsuranController::class);
    Route::resource('/admin/simpanan', SimpananController::class);
    Route::resource('/admin/petugas', PetugasController::class);

    Route::put('/nasabah/update-checkbox/{id}', [NasabahController::class, 'updateCheckbox'])->name('nasabah.updateCheckbox');

    Route::get('/get-pinjaman/{nasabah_id}', [AnggsuranController::class, 'getPinjaman']);

    Route::get('/admin/laporan/simpanan', [LaporanController::class, 'LaporanSimpanan'])->name('laporan.simpanan');
    Route::get('/admin/laporan/pinjaman', [LaporanController::class, 'LaporanPinjaman'])->name('laporan.pinjaman');
    Route::get('/admin/laporan/angsuran', [LaporanController::class, 'LaporanAngsuran'])->name('laporan.angsuran');

    Route::get('/admin/pdf/simpanan', [PdfController::class, 'simpananPdf'])->name('pdf.simpanan');
    Route::get('/admin/pdf/pinjaman', [PdfController::class, 'pinjamanPdf'])->name('pdf.pinjaman');
    Route::get('/admin/pdf/angsuran', [PdfController::class, 'angsuranPdf'])->name('pdf.angsuran');

    Route::post('/admin/nasabah/{id}',[NasabahController::class,'verify'])->name('nasabah.verify');
    Route::post('/angsuran/update-status/{id}', [AnggsuranController::class, 'updateStatus'])->name('angsuran.updateStatus');
    Route::get('/get-max-pinjaman/{nasabah_id}', [PinjamanController::class, 'getMaxPinjaman'])->name('pinjaman.getMaxPinjaman');
    Route::get('/pinjaman/check-eligibility/{id}', [PinjamanController::class, 'checkEligibility']);

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
    Route::resource('/admin/pinjaman', PinjamanController::class);
    Route::resource('/admin/angsuran', AnggsuranController::class);
    Route::resource('/admin/simpanan', SimpananController::class);
    Route::resource('/admin/petugas', PetugasController::class);
    Route::get("/profil",[Daftar::class,'profil'])->name("user.profil");
    Route::get("/profil/edit",[Daftar::class,'edit'])->name('user.edit');
    Route::Put('/profil/{id}',[Daftar::class,'update'])->name('user.update');
});
