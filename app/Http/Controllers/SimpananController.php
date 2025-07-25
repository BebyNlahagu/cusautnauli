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
                'tanggal_transaksi' => $items->max('created_at')->translatedFormat('F'),
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
                    'tanggal' => $simpan->created_at->translatedFormat('F'),
                ];
            });

        return response()->json(['simpans' => $simpans]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_simpanan' => 'required|string',
            'jumlah_simpanan' => 'nullable|numeric',
        ]);

        $userId = $request->user_id;
        $jenisSimpanan = $request->jenis_simpanan; 
        $now = Carbon::now();
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;

        if ($jenisSimpanan === 'Simpanan Wajib') {
            $tanggalBergabung = User::find($userId)->created_at;

            $simpananTerakhir = Simpanan::where('user_id', $userId)
                ->where('jenis_simpanan', 'simpanan wajib')
                ->orderBy('created_at', 'desc')
                ->first();

            $bulanTerakhirSimpan = $simpananTerakhir ? $simpananTerakhir->created_at->month : $tanggalBergabung->month;
            $tahunTerakhirSimpan = $simpananTerakhir ? $simpananTerakhir->created_at->year : $tanggalBergabung->year;

            $mulai = Carbon::create($tahunTerakhirSimpan, $bulanTerakhirSimpan, 1);
            $selesai = Carbon::create($tahunSekarang, $bulanSekarang, 1);

            $jumlahSimpananAwal = 50000;
            $potongan = 0.02 * $jumlahSimpananAwal;
            $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

            $jumlahBulanDitambahkan = 0;

            $tanggalIterasi = $mulai->copy();

            while ($tanggalIterasi <= $selesai) {
                $bulan = $tanggalIterasi->month;
                $tahun = $tanggalIterasi->year;

                $sudahAda = Simpanan::where('user_id', $userId)
                    ->where('jenis_simpanan', 'simpanan wajib')
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->exists();

                if (!$sudahAda) {
                    Simpanan::create([
                        'user_id' => $userId,
                        'jumlah_simpanan' => $jumlahSetelahPotong,
                        'jumlah_kapitalisasi' => $potongan,
                        'jenis_simpanan' => 'simpanan wajib',
                        'created_at' => $tanggalIterasi,
                        'updated_at' => $tanggalIterasi,
                    ]);

                    Simpan::create([
                        'user_id' => $userId,
                        'nama_simpanan' => 'simpanan wajib',
                        'besar_simpanan' => $jumlahSetelahPotong,
                        'created_at' => $tanggalIterasi,
                        'updated_at' => $tanggalIterasi,
                    ]);

                    $jumlahBulanDitambahkan++;
                }

                $tanggalIterasi->addMonth();
            }
            if ($jumlahBulanDitambahkan === 0) {
                return redirect()->back()->with('error', 'Simpanan Wajib bulan ini sudah dibayar.');
            }
            return redirect()->route('simpanan.index')->with('success', 'Simpanan Wajib berhasil ditambahkan untuk ' . $jumlahBulanDitambahkan . ' bulan tertunggak.');
        }
        else {
            $sudahBayarTahunIni = Simpanan::where('user_id', $userId)
                ->where('jenis_simpanan', $jenisSimpanan)
                ->whereYear('created_at', $tahunSekarang)
                ->exists();

            if ($sudahBayarTahunIni) {
                return redirect()->back()->with('error', 'Jenis Simpanan ' . $jenisSimpanan . ' hanya bisa dibayar sekali dalam setahun dan sudah dibayar.');
            }

            $jumlahSimpananAwal = 50000;
            $potongan = 0.02 * $jumlahSimpananAwal;
            $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

            Simpanan::create([
                'user_id' => $userId,
                'jumlah_simpanan' => $jumlahSetelahPotong,
                'jumlah_kapitalisasi' => $potongan,
                'jenis_simpanan' => $jenisSimpanan,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Simpan::create([
                'user_id' => $userId,
                'nama_simpanan' => $jenisSimpanan,
                'besar_simpanan' => $jumlahSetelahPotong,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return redirect()->route('simpanan.index')->with('success', '' . $jenisSimpanan . ' berhasil ditambahkan untuk tahun ini.');
        }
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
