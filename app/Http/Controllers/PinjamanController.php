<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
   public function index()
   {
      $nasabah = Nasabah::all();
      $nasabah = $nasabah ?: collect();
      $pinjaman = Pinjaman::with("nasabah")->get();
      return view('admin.pinjaman.index', compact('pinjaman', 'nasabah'));
   }

   public function store(Request $request)
   {

      $request->validate([
         'nasabah_id' => 'required|exists:nasabahs,id',
         'lama_pinjaman' => 'required|in:5 Bulan,10 Bulan,15 Bulan,20 Bulan,25 Bulan,30 Bulan',
         'jumlah_pinjaman' => 'required|numeric|min:0',
         'bunga_pinjaman' => 'required|numeric|min:0',
         'simpanan' => 'nullable|numeric|min:0',
         // 'adm' => 'nullable|numeric|min:0',
      ]);

      DB::beginTransaction();

      try {
         $nasabah = Nasabah::findOrFail($request->nasabah_id);
         $bergabung_sejak = Carbon::parse($nasabah->tanggal_masuk);
         $sekarang = now();
         $selisih_bulan = $bergabung_sejak->diffInMonths($sekarang);

         if ($selisih_bulan < 6) {
            return redirect()->back()->withErrors(['nasabah_id' => 'Nasabah belum bergabung minimal 6 bulan.']);
         }

         $total_simpanan = Simpanan::where('nasabah_id', $request->nasabah_id)->sum('jumlah_simpanan');
         $max_pinjaman = $total_simpanan * 5;

         if ($request->jumlah_pinjaman > $max_pinjaman) {
            return redirect()->back()->withErrors(['jumlah_pinjaman' => 'Jumlah pinjaman tidak boleh lebih dari 5x total simpanan.']);
         }

         $potongan = $max_pinjaman * (2 / 100);
         $adm = $max_pinjaman * (0.5 / 100);
         $total_terima = $max_pinjaman - ($potongan + $adm);
         $bunga_perbulan = 3;

         $pinjaman = Pinjaman::create([
            'nasabah_id' => $request->nasabah_id,
            'lama_pinjaman' => $request->lama_pinjaman,
            'jumlah_pinjaman' => $max_pinjaman,
            'bunga_pinjaman' => $bunga_perbulan,
            'kapitalisasi' => $potongan,
            'proposi' => $adm,
            'terima_total' => $total_terima,
         ]);

         $simpanan = Simpanan::where('nasabah_id', $request->nasabah_id)
            ->where('jenis_simpanan', 'Simpanan Kapitalisasi')
            ->first();

         if ($simpanan) {
            $simpanan->jumlah_simpanan += $potongan;
            $simpanan->save();
         } else {
            Simpanan::create([
               'nasabah_id' => $request->nasabah_id,
               'jenis_simpanan' => 'Simpanan Kapitalisasi',
               'jumlah_simpanan' => $potongan,
            ]);
         }

         DB::commit();

         return redirect()->route('pinjaman.index')->with('success', 'Data pinjaman dan kapitalisasi berhasil disimpan.');
      } catch (\Exception $e) {
         DB::rollback();
         return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
      }

      return redirect()->route('pinjaman.index')->with('success', 'Data Berhasil Di Tambahkan');
   }

   public function getMaxPinjaman($nasabah_id)
   {
      $nasabah = Nasabah::find($nasabah_id);

      if (!$nasabah) {
         return response()->json(['error' => 'Nasabah tidak ditemukan.'], 404);
      }

      $total_simpanan = Simpanan::where('nasabah_id', $nasabah->id)->sum('jumlah_simpanan');
      $max_pinjaman = $total_simpanan * 5;

      return response()->json(['max_pinjaman' => $max_pinjaman]);
   }


   public function edit($id)
   {
      $pinjaman = Pinjaman::findOrFail($id);
      return redirect()->route('pinjaman.index', compact('pinjaman'));
   }

   public function update(Request $request, $id)
   {
      $request->validate([
         'nasabah_id' => 'nullable|exists:nasabahs,id',
         'lama_pinjaman' => 'nullable|in:5 Bulan,10 Bulan,15 Bulan,20 Bulan,25 Bulan,30 Bulan',
         'jumlah_pinjaman' => 'nullable|numeric|min:0',
         'bunga_pinjaman' => 'nullable|numeric|min:0',
         'simpanan' => 'nullable|numeric|min:0',
         'adm' => 'nullable|numeric|min:0',
      ]);


      $potongan = $request->jumlah_pinjaman * (2 / 100);
      $adm = $request->jumlah_pinjaman * (0.5 / 100);
      $total_terima = $request->jumlah_pinjaman - ($potongan + $adm);

      $bunga_perbulan = 3;
      Pinjaman::findOrFail($id);

      Pinjaman::create([
         'nasabah_id' => $request->nasabah_id,
         'lama_pinjaman' => $request->lama_pinjaman,
         'jumlah_pinjaman' => $request->jumlah_pinjaman,
         'bunga_pinjaman' => $bunga_perbulan,
         'kapitalisasi' => $potongan,
         'proposi' => $adm,
         'terima_total' => $total_terima,
      ]);

      return redirect()->route('pinjaman.index')->with('success', 'Data Berhasil Di Perbarui');
   }

   public function destroy($id)
   {
      Pinjaman::findorfail($id)->delete();
      return redirect()->route('pinjaman.index')->with('delete', 'Data Berhasil Dihapus');
   }
}
