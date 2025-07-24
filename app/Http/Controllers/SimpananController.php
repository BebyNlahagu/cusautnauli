<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Simpan;
use App\Models\Simpanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpananController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $simpanan = Simpanan::with('user')->latest()->get();

        $simpananGrouped = $simpanan->groupBy('user_id')->map(function ($items) {
            return [
                'user' => $items->first()->user,
                'tanggal_transaksi' => $items->max('created_at')->translatedFormat('l, d F Y'),
                'jenis_simpanan' => $items->pluck('jenis_simpanan')->implode(', '),
                'total_simpanan' => $items->sum('jumlah_simpanan'),
                'total_kapitalisasi' => $items->sum('jumlah_kapitalisasi'),
            ];
        });

        $kapitalisasi = $simpanan->sum('jumlah_kapitalisasi');
        $nasabah = User::where('status', 'Verify')->get();

        if ($user->role !== 'Admin') {
            $simpananGrouped = $simpananGrouped->filter(function ($item) use ($user) {
                return $item['user']->id === $user->id;
            });

            $kapitalisasi = $simpanan->where('user_id', $user->id)->sum('jumlah_kapitalisasi');
            $nasabah = User::where('id', $user->id)->get();
        }

        $jumlah = Simpanan::sum("jumlah_simpanan");

        return view("admin.simpanan.index", compact('simpananGrouped', 'kapitalisasi', 'nasabah', 'simpanan', 'jumlah'));
    }

    public function getUserSimpanan($id)
    {
        $simpans = Simpan::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($simpan) {
                return [
                    'nama_simpanan' => $simpan->nama_simpanan,
                    'besar_simpanan' => number_format($simpan->besar_simpanan, 0, ',', '.'),
                    'tanggal' => $simpan->created_at->translatedFormat('d F Y'),
                ];
            });

        return response()->json(['simpans' => $simpans]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $userId = $request->user_id;
        $now = Carbon::now();
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;

        // Ambil simpanan terakhir user
        $simpananTerakhir = Simpanan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Jika user belum pernah menyimpan, ambil dari created_at user (bulan bergabung)
        $tanggalBergabung = User::find($userId)->created_at;
        $bulanBergabung = $tanggalBergabung->month;
        $tahunBergabung = $tanggalBergabung->year;

        // Hitung bulan terakhir menyimpan
        $bulanTerakhirSimpan = $simpananTerakhir ? $simpananTerakhir->created_at->month : $bulanBergabung;
        $tahunTerakhirSimpan = $simpananTerakhir ? $simpananTerakhir->created_at->year : $tahunBergabung;

        // Hitung total bulan yang belum dibayar
        $mulai = Carbon::create($tahunTerakhirSimpan, $bulanTerakhirSimpan, 1);
        $selesai = Carbon::create($tahunSekarang, $bulanSekarang, 1);

        $selisihBulan = $selesai->diffInMonths($mulai);

        if ($selisihBulan < 1) {
            return redirect()->back()->with('error', 'Simpanan hanya bisa dilakukan satu kali per bulan.');
        }

        // Mulai simpan simpanan untuk tiap bulan tertinggal
        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        for ($i = 1; $i <= $selisihBulan; $i++) {
            $tanggal = $mulai->copy()->addMonths($i);

            Simpanan::create([
                'user_id' => $userId,
                'jumlah_simpanan' => $jumlahSetelahPotong,
                'jumlah_kapitalisasi' => $potongan,
                'jenis_simpanan' => $request->jenis_simpanan,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);

            Simpan::create([
                'user_id' => $userId,
                'nama_simpanan' => $request->jenis_simpanan ?? 'Tidak diketahui',
                'besar_simpanan' => $jumlahSetelahPotong,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);
        }

        return redirect()->route('simpanan.index')->with('success', 'Simpanan berhasil ditambahkan untuk ' . $selisihBulan . ' bulan tertunggak.');
    }


    public function edit($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        return redirect()->route('simpanan.index', compact('simpanan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'jenis_simpanan' => 'nullable',
            'jumlah_simpanan' => 'nullable|numeric',
            'total' => 'nullable',
        ]);

        $simpanan = Simpanan::findOrFail($id);

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;


        Simpanan::create([
            'user_id' => $request->user_id,
            'jumlah_simpanan' => $jumlahSetelahPotong,
            'jumlah_kapitalisasi' => $potongan,
            'jenis_simpanan' => $request->jenis_simpanan,
        ]);

        return redirect()->route('simpanan.index')->with('success', 'Data Berhasil Di Perbarui');
    }

    public function destroy($id)
    {
        try {
            $simpanan = Simpanan::findOrFail($id);
            $simpanan->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
