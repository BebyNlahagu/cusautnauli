<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
   public function index()
   {
   
      $nasabah = Nasabah::all();

      if(auth()->user()->role == "Admin")
      {
         $pinjaman = Pinjaman::all();
      }else
      {
        $pinjaman = Pinjaman::where('nasabah_id', auth()->user()->nasabah_id)->get();
      }
      return view('admin.pinjaman.index', compact('pinjaman', 'nasabah'));
   }

   public function checkEligibility($nasabah_id)
   {
      $nasabah = Nasabah::find($nasabah_id);

      if (!$nasabah) {
         return response()->json(['error' => 'Nasabah tidak ditemukan.'], 404);
      }

      $selisih_bulan = Carbon::parse($nasabah->created_at)->diffInMonths(now());

      if ($selisih_bulan < 6) {
         return response()->json([
            'status' => 'not_eligible',
            'message' => 'Nasabah belum bergabung minimal 6 bulan.',
         ]);
      }

      $umur = Carbon::parse($nasabah->tanggal_lahir)->diffInYears(now());
      if ($umur < 17) {
         return response()->json([
            'status' => 'not_eligible',
            'message' => 'Nasabah belum berumur 17 tahun.',
         ]);
      }

      $adaAngsuranBelumLunas = DB::table('angsuran')
         ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
         ->where('pinjaman.nasabah_id', $nasabah->id)
         ->where('angsuran.status', '!=', 'Lunas')
         ->exists();

      if ($adaAngsuranBelumLunas) {
         return response()->json([
            'status' => 'not_eligible',
            'message' => 'Nasabah masih memiliki angsuran yang belum lunas.'
         ]);
      }


      $total_simpanan = Simpanan::where('nasabah_id', $nasabah->id)->sum('jumlah_simpanan');
      $jumlah_pinjaman = $total_simpanan * 5;
      $bunga_pinjaman = 3;

      return response()->json([
         'status' => 'eligible',
         'nama_nasabah' => $nasabah->name,
         'jumlah_pinjaman' => $jumlah_pinjaman,
         'bunga_pinjaman' => $bunga_pinjaman,
         'umur' => $umur,
         'lama_gabung_bulan' => $selisih_bulan,
         'angsuran' => $adaAngsuranBelumLunas,
      ]);
   }

   public function store(Request $request)
   {
      $request->validate([
         'nasabah_id' => 'required|exists:nasabahs,id',
         'lama_pinjaman' => 'required|in:5 Bulan,10 Bulan,15 Bulan,20 Bulan,25 Bulan,30 Bulan',
         'jumlah_pinjaman' => 'required|numeric|min:0',
         'bunga_pinjaman' => 'required|numeric|min:0',
         'kapitalisasi' => 'nullable|numeric|min:0',
         'proposi' => 'required|numeric|min:0',
         'terima_total' => 'required|numeric|min:0',
         'total_pinjaman' => 'nullable|numeric|min:0'
      ]);

      DB::beginTransaction();

      try {

         Nasabah::findOrFail($request->nasabah_id);


         $total_simpanan = Simpanan::where('nasabah_id', $request->nasabah_id)->sum('jumlah_simpanan');
         $maksimal_pinjaman = $total_simpanan * 5;

         if ($request->jumlah_pinjaman > $maksimal_pinjaman) {
            return back()->withErrors([
               'jumlah_pinjaman' => 'Jumlah pinjaman melebihi batas maksimal yang diperbolehkan (' . number_format($maksimal_pinjaman, 0, ',', '.') . ')'
            ])->withInput();
         }

         Pinjaman::create([
            'nasabah_id' => $request->nasabah_id,
            'lama_pinjaman' => $request->lama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'bunga_pinjaman' => $request->bunga_pinjaman,
            'proposi' => $request->proposi,
            'terima_total' => $request->terima_total
         ]);

         DB::commit();

         return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil disimpan.');
      } catch (\Exception $e) {
         DB::rollback();
         return back()->withErrors('Terjadi kesalahan: ' . $e->getMessage())->withInput();
      }
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


      // $potongan = $request->jumlah_pinjaman * (2 / 100);
      $adm = $request->jumlah_pinjaman * (0.5 / 100);
      $total_terima = $request->jumlah_pinjaman -  $adm;

      $bunga_perbulan = 3;
      Pinjaman::findOrFail($id);

      Pinjaman::create([
         'nasabah_id' => $request->nasabah_id,
         'lama_pinjaman' => $request->lama_pinjaman,
         'jumlah_pinjaman' => $request->jumlah_pinjaman,
         'bunga_pinjaman' => $bunga_perbulan,
         // 'kapitalisasi' => $potongan,
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
