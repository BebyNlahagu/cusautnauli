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

        $targetUser = $user->role == 'Admin' ? $nasabah->first() : $user;

        $tanggalGabung = $targetUser->created_at;
        $tahunGabung = $tanggalGabung->year;
        $bulanGabung = $tanggalGabung->month;

        $now = \Carbon\Carbon::now();
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;

        $availableMonths = [];
        $tempDate = \Carbon\Carbon::create($tahunGabung, $bulanGabung, 1);

        while ($tempDate->lessThanOrEqualTo($now)) {
            $availableMonths[] = $tempDate->format('Y-m');
            $tempDate->addMonth();
        }


        $bulanTerbayar = Simpanan::where('user_id', $targetUser->id)
            ->where('jenis_simpanan', 'Simpanan Wajib')
            ->pluck('created_at')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m');
            })->toArray();
        $jenisSimpanan = 'Simpanan Wajib';

        return view("admin.simpanan.index", compact(
            'simpananGrouped',
            'kapitalisasi',
            'nasabah',
            'simpanan',
            'jumlah',
            'targetUser',
            'tahunGabung',
            'bulanGabung',
            'bulanSekarang',
            'availableMonths',
            'bulanTerbayar',
            'jenisSimpanan'
        ));
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
            'bulan_transaksi' => 'nullable|array',
        ]);

        $userId = $request->user_id;
        $jenisSimpanan = $request->jenis_simpanan;
        $now = Carbon::now();

        $jumlahSimpananAwal = 50000;
        $potongan = 0.02 * $jumlahSimpananAwal;
        $jumlahSetelahPotong = $jumlahSimpananAwal - $potongan;

        if ($request->has('bulan_transaksi') && is_array($request->bulan_transaksi)) {
            if ($jenisSimpanan === 'Simpanan Wajib') {
                $berhasil = 0;
                $duplikat = [];

                foreach ($request->bulan_transaksi as $bulan) {
                    $tanggal = Carbon::parse($bulan . '-01');

                    $sudahAda = Simpanan::where('user_id', $userId)
                        ->where('jenis_simpanan', 'Simpanan Wajib')
                        ->whereMonth('created_at', $tanggal->month)
                        ->whereYear('created_at', $tanggal->year)
                        ->exists();

                    if (!$sudahAda) {
                        Simpanan::create([
                            'user_id' => $userId,
                            'jumlah_simpanan' => $jumlahSetelahPotong,
                            'jumlah_kapitalisasi' => $potongan,
                            'jenis_simpanan' => 'Simpanan Wajib',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        Simpan::create([
                            'user_id' => $userId,
                            'nama_simpanan' => 'Simpanan Wajib',
                            'besar_simpanan' => $jumlahSetelahPotong,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        $berhasil++;
                    } else {
                        $duplikat[] = $tanggal->translatedFormat('F Y');
                    }
                }

                if ($berhasil > 0) {
                    return redirect()->route('simpanan.index')->with('success', "Simpanan Wajib berhasil ditambahkan untuk $berhasil bulan.");
                } else {
                    return redirect()->back()->with('error', 'Simpanan Wajib sudah dibayar untuk bulan yang dipilih: ' . implode(', ', $duplikat));
                }
            } else {
                $tahunDipilih = Carbon::parse($request->bulan_transaksi[0] . '-01')->year;

                $sudahBayar = Simpanan::where('user_id', $userId)
                    ->where('jenis_simpanan', $jenisSimpanan)
                    ->whereYear('created_at', $tahunDipilih)
                    ->exists();

                if ($sudahBayar) {
                    return redirect()->back()->with('error', "$jenisSimpanan sudah dibayar untuk tahun $tahunDipilih.");
                }

                $tanggalSimpan = Carbon::createFromDate($tahunDipilih, 1, 1);

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

                return redirect()->route('simpanan.index')->with('success', "$jenisSimpanan berhasil ditambahkan untuk tahun $tahunDipilih.");
            }
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
