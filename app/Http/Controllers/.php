<?php

namespace App\Http\Controllers;

use App\Models\Anggsuran;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Admin') {
            $pinjaman = Pinjaman::latest()->get();
            $nasabah = User::where('status', 'Verify')->get();
        } else {
            $pinjaman = Pinjaman::where('user_id', $user->id)->latest()->get();
            $nasabah = User::where('id', $user->id)->get();
        }

        return view('admin.pinjaman.index', compact('pinjaman', 'nasabah'));
    }

    public function checkEligibility($user_id)
    {
        try {
            $nasabah = User::find($user_id);

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

            if (!$nasabah->tanggal_lahir) {
                return response()->json(['error' => 'Data tanggal lahir tidak tersedia.'], 422);
            }

            $umur = Carbon::parse($nasabah->tanggal_lahir)->diffInYears(now());
            if ($umur < 17) {
                return response()->json([
                    'status' => 'not_eligible',
                    'message' => 'Nasabah belum berumur 17 tahun.',
                ]);
            }

            $adaAngsuranBelumLunas = DB::table('angsuran')->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')->where('pinjaman.user_id', $nasabah->id)->where('angsuran.status', '!=', 'Lunas')->exists();

            if ($adaAngsuranBelumLunas) {
                return response()->json([
                    'status' => 'not_eligible',
                    'message' => 'Nasabah masih memiliki angsuran yang belum lunas.',
                ]);
            }

            $total_simpanan = Simpanan::where('user_id', $nasabah->id)->sum('jumlah_simpanan');
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function ubahStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:Disetujui,Ditolak',
        ]);

        try {
            $pinjaman = Pinjaman::findOrFail($id);
            $pinjaman->update([
                'status' => $request->status,
            ]);

            return redirect()->route('pinjaman.index')->with('success', 'Pengajuan Pinjaman berhasil di setujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Pengajuan Pinjama di tolak : ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lama_pinjaman' => 'required|in:6 Bulan,12 Bulan,18 Bulan, 24 Bulan, 30 Bulan, 36 Bulan',
            'jumlah_pinjaman' => 'required',
            'nama_penjamin' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);

            $selisih_bulan = Carbon::parse($user->created_at)->diffInMonths(now());
            $umur = Carbon::parse($user->tanggal_lahir)->diffInYears(now());

            if ($selisih_bulan < 6 || $umur < 17) {
                return back()->withInput()->with('error', 'Nasabah belum memenuhi syarat pengajuan pinjaman.');
            }

            $adaBelumLunas = Anggsuran::join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')->where('pinjaman.user_id', $user->id)->where('angsuran.status', '!=', 'Lunas')->exists();

            if ($adaBelumLunas) {
                return back()->withInput()->with('error', 'Masih ada angsuran yang belum lunas.');
            }

            $total_simpanan = Simpanan::where('user_id', $user->id)->sum('jumlah_simpanan');
            $maksimal_pinjaman = $total_simpanan * 5;

            if ($request->jumlah_pinjaman > $maksimal_pinjaman) {
                return back()
                    ->withInput()
                    ->with('error', 'Jumlah pinjaman melebihi batas maksimal (' . number_format($maksimal_pinjaman, 0, ',', '.') . ').');
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
        return redirect()->route('pinjaman.index', compact('pinjaman'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'lama_pinjaman' => 'nullable|in:5 Bulan,10 Bulan,15 Bulan,20 Bulan,25 Bulan,30 Bulan',
            'jumlah_pinjaman' => 'nullable|numeric|min:0',
            'bunga_pinjaman' => 'nullable|numeric|min:0',
            'simpanan' => 'nullable|numeric|min:0',
            'adm' => 'nullable|numeric|min:0',
        ]);

        // $potongan = $request->jumlah_pinjaman * (2 / 100);
        $adm = $request->jumlah_pinjaman * (0.5 / 100);
        $total_terima = $request->jumlah_pinjaman - $adm;

        $bunga_perbulan = 3;
        Pinjaman::findOrFail($id);

        Pinjaman::create([
            'user_id' => $request->user_id,
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
