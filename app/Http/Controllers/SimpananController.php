<?php

namespace App\Http\Controllers;

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
        $simpanan = Simpanan::with('user')->latest()->orderBy('user_id', 'asc')->get();

        $simpananGrouped = $simpanan->groupBy('user_id')->map(function ($items) {
            return [
                'user' => $items->first()->user,
                'tanggal_transaksi' => $items->max('created_at')->translatedFormat('F'),
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

        $jumlah = Simpanan::sum('jumlah_simpanan');

        return view('admin.simpanan.index', compact('simpananGrouped', 'kapitalisasi', 'nasabah', 'simpanan', 'jumlah'));
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

            $simpananTerakhir = Simpanan::where('user_id', $userId)->where('jenis_simpanan', 'Simpanan Wajib')->orderBy('created_at', 'desc')->first();

            // Mulai dari bulan setelah simpanan terakhir, atau dari tanggal bergabung jika belum ada simpanan
            if ($simpananTerakhir) {
                $mulai = $simpananTerakhir->created_at->copy()->addMonth()->startOfMonth();
            } else {
                $mulai = $tanggalBergabung->copy()->startOfMonth();
            }

            $selesai = $now->copy()->startOfMonth();

            $jumlahSimpananAwal = 50000;
            $potongan = 0.02 * $jumlahSimpananAwal;
            $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

            $jumlahBulanDitambahkan = 0;

            // Loop dari bulan mulai sampai bulan selesai
            $tanggalIterasi = $mulai->copy();
            while ($tanggalIterasi <= $selesai) {
                $bulan = $tanggalIterasi->month;
                $tahun = $tanggalIterasi->year;

                $sudahAda = Simpanan::where('user_id', $userId)->where('jenis_simpanan', 'Simpanan Wajib')->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->exists();

                if (!$sudahAda) {
                    Simpanan::create([
                        'user_id' => $userId,
                        'jumlah_simpanan' => $jumlahSetelahPotong,
                        'jumlah_kapitalisasi' => $potongan,
                        'jenis_simpanan' => 'Simpanan Wajib',
                        'created_at' => $tanggalIterasi->copy(),
                        'updated_at' => $tanggalIterasi->copy(),
                    ]);

                    Simpan::create([
                        'user_id' => $userId,
                        'nama_simpanan' => 'Simpanan Wajib',
                        'besar_simpanan' => $jumlahSetelahPotong,
                        'created_at' => $tanggalIterasi->copy(),
                        'updated_at' => $tanggalIterasi->copy(),
                    ]);

                    $jumlahBulanDitambahkan++;
                }

                $tanggalIterasi->addMonth();
            }

            if ($jumlahBulanDitambahkan === 0) {
                return redirect()->back()->with('error', 'Simpanan Wajib bulan ini sudah dibayar.');
            }

            return redirect()
                ->route('simpanan.index')
                ->with('success', 'Simpanan Wajib berhasil ditambahkan untuk ' . $jumlahBulanDitambahkan . ' bulan tertunggak.');
        } else {
            $sudahBayarTahunIni = Simpanan::where('user_id', $userId)->where('jenis_simpanan', $jenisSimpanan)->whereYear('created_at', $tahunSekarang)->exists();

            if ($sudahBayarTahunIni) {
                return redirect()
                    ->back()
                    ->with('error', 'Jenis Simpanan ' . $jenisSimpanan . ' hanya bisa dibayar sekali dalam setahun dan sudah dibayar.');
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

            return redirect()
                ->route('simpanan.index')
                ->with('success', $jenisSimpanan . ' berhasil ditambahkan untuk tahun ini.');
        }
    }

    public function getUserSimpanan($id)
    {
        $simpans = Simpanan::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($simpan) {
                return [
                    'id' => $simpan->id,
                    'user_id' => $simpan->user_id,
                    'jenis_simpanan' => $simpan->jenis_simpanan,
                    'jumlah_simpanan' => $simpan->jumlah_simpanan,
                    'tanggal' => $simpan->created_at->translatedFormat('F'),
                    'status' => $simpan->status,
                ];
            });

        return response()->json(['simpanan' => $simpans]);
    }

    public function paid($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        $user = Auth::user();

        \Midtrans\Config::$serverKey = config('midtrans.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'SIMPANAN-' . $simpanan->id . '-' . time(),
                'gross_amount' => $simpanan->jumlah_simpanan,
            ],
            'customer_details' => [
                'first_name' => $simpanan->user->name,
                'email' => $simpanan->user->email,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function konfirmasi($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        $simpanan->status = 'Lunas';
        $simpanan->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        $simpanan->delete();
        return response()->json(['success' => true]);
    }

    public function payAll(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada simpanan yang dipilih'], 400);
        }

        $simpanan = Simpanan::whereIn('id', $ids)->get();
        $totalBayar = $simpanan->sum('jumlah_simpanan');

        // Setup Midtrans config
        \Midtrans\Config::$serverKey = config('midtrans.midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'PAYALL-' . uniqid(),
                'gross_amount' => $totalBayar,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json(['snap_token' => $snapToken]);
    }

    public function payAllSuccess(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada simpanan'], 400);
        }

        Simpanan::whereIn('id', $ids)->update(['status' => 'Lunas']);

        return response()->json(['success' => true]);
    }
}
