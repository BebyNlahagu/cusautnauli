<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PinjamanController extends Controller
{
   public function index()
   {
      $user = Auth::user();

      if ($user->role === "Admin") {
         $pinjaman = Pinjaman::latest()->get();
         $nasabah = User::where('status', 'Verify')->get();
      } else {
         $pinjaman = Pinjaman::where('user_id', $user->id)->latest()->get();
         $nasabah = User::where('id', $user->id)->get();
      }

      return view('admin.pinjaman.index', compact('pinjaman', 'nasabah'));
   }

   public function create()
   {
      $user = Auth::user();

      $selisih_bulan = Carbon::parse($user->created_at)->diffInMonths(now());
      $umur = Carbon::parse($user->tanggal_lahir)->diffInYears(now());

      if ($selisih_bulan < 6 || $umur < 17) {
         return redirect()->route('pinjaman.index')->with('error', 'Anda belum memenuhi syarat pengajuan pinjaman.');
      }

      $jumlahMinimal = Simpanan::where('user_id', $user->id)->where('jenis_simpanan', 'Simpanan Wajib')->sum('jumlah_simpanan');
      $total_simpanan = Simpanan::where('user_id', $user->id)->sum('jumlah_simpanan');

      return view('admin.pinjaman.create', compact('user', 'total_simpanan', 'jumlahMinimal'));
   }

   public function ubahStatus(Request $request, $id)
   {
      $request->validate([
         'status' => 'required|in:Disetujui,Ditolak'
      ]);

      try {
         $pinjaman = Pinjaman::findOrFail($id);
         $pinjaman->update([
            'status' => $request->status
         ]);

         return redirect()->route('pinjaman.index')->with('success', 'Status pengajuan berhasil diubah.');
      } catch (\Exception $e) {
         return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
      }
   }

   public function store(Request $request)
   {

      $request->validate([
         'user_id' => 'required|exists:users,id',
         'lama_pinjaman' => 'required|in:6 Bulan',
         'jumlah_pinjaman' => 'required',
         'nama_penjamin' => 'required|string',
         'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
      ]);

      DB::beginTransaction();

      try {

         $user = User::findOrFail($request->user_id);

         $selisih_bulan = Carbon::parse($user->created_at)->diffInMonths(now());
         $umur = Carbon::parse($user->tanggal_lahir)->diffInYears(now());

         if ($selisih_bulan < 6 || $umur < 17) {
            return back()->withInput()->with('error', "Nasabah belum memenuhi syarat pengajuan pinjaman.");
         }

         $adaBelumLunas = Anggsuran::join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.user_id', $user->id)
            ->where('angsuran.status', '!=', 'Lunas')
            ->exists();

         if ($adaBelumLunas) {
            return back()->withInput()->with('error', "Masih ada angsuran yang belum lunas.");
         }

         $total_simpanan = Simpanan::where('user_id', $user->id)->sum('jumlah_simpanan');
         $maksimal_pinjaman = $total_simpanan * 5;

         if ($request->jumlah_pinjaman > $maksimal_pinjaman) {
            return back()->withInput()->with('error', "Jumlah pinjaman melebihi batas maksimal (" . number_format($maksimal_pinjaman, 0, ',', '.') . ").");
         }


         $foto = null;
         if ($request->hasFile('foto')) {
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('images', $foto, 'public');
         }

         $bunga = 3;
         $proposi = $request->jumlah_pinjaman * 0.005;
         $terima_total = $request->jumlah_pinjaman - $proposi;

         Pinjaman::create([
            'user_id' => $user->id,
            'lama_pinjaman' => $request->lama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'bunga_pinjaman' => $bunga,
            'proposi' => $proposi,
            'terima_total' => $terima_total,
            'foto' => $foto,
            'status' => 'Pending',
         ]);

         DB::commit();

         return redirect()->route('pinjaman.index')->with('success', 'pengajuan anda berhasil, untuk pencairan agar datang ke kantor cu saut maju nauli');
      } catch (\Exception $e) {
         DB::rollBack();
         return back()->withInput()->with('error', $e->getMessage());
      }
   }

   public function edit($id)
   {
      $pinjaman = Pinjaman::findOrFail($id);
      return view('admin.pinjaman.edit', compact('pinjaman'));
   }

   public function update(Request $request, $id)
   {
      $request->validate([
         'lama_pinjaman' => 'required|in:5 Bulan,10 Bulan,15 Bulan,20 Bulan,25 Bulan,30 Bulan',
         'jumlah_pinjaman' => 'required|numeric',
         'bunga_pinjaman' => 'required|numeric',
      ]);

      try {
         $pinjaman = Pinjaman::findOrFail($id);

         $pinjaman->update([
            'lama_pinjaman' => $request->lama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'bunga_pinjaman' => $request->bunga_pinjaman,
         ]);

         return redirect()->route('pinjaman.index')->with('success', 'Data Berhasil Di Perbarui');
      } catch (\Exception $e) {
         return back()->withErrors(['error' => $e->getMessage()]);
      }
   }

   public function destroy($id)
   {
      try {
         $pinjaman = Pinjaman::findOrFail($id);
         $pinjaman->delete();
         return redirect()->route('pinjaman.index')->with('delete', 'Data Berhasil Dihapus');
      } catch (\Exception $e) {
         return redirect()->route('pinjaman.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
      }
   }

   public function checkEligibility($user_id)
   {
      try {
         $nasabah = User::find($user_id);
         if (!$nasabah) {
            return response()->json(['error' => 'Nasabah tidak ditemukan'], 404);
         }

         $selisih_bulan = Carbon::parse($nasabah->created_at)->diffInMonths(now());
         $umur = Carbon::parse($nasabah->tanggal_lahir)->diffInYears(now());

         if ($selisih_bulan < 6 || $umur < 17) {
            return response()->json([
               'status' => 'not_eligible',
               'message' => 'Nasabah belum memenuhi syarat minimal usia atau lama bergabung.',
            ]);
         }

         $adaBelumLunas = Anggsuran::join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.user_id', $nasabah->id)
            ->where('angsuran.status', '!=', 'Lunas')
            ->exists();

         if ($adaBelumLunas) {
            return response()->json([
               'status' => 'not_eligible',
               'message' => 'Masih ada angsuran yang belum lunas.',
            ]);
         }

         $total_simpanan = Simpanan::where('user_id', $nasabah->id)->sum('jumlah_simpanan');
         $maksimal_pinjaman = $total_simpanan * 5;

         return response()->json([
            'status' => 'eligible',
            'jumlah_pinjaman' => $maksimal_pinjaman,
            'bunga_pinjaman' => 3,
         ]);
      } catch (\Exception $e) {
         return response()->json(['error' => $e->getMessage()], 500);
      }
   }
}
