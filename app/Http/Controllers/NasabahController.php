<?php

namespace App\Http\Controllers;

use App\Notifications\DataVerify;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class NasabahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('addData');
    }

    public function index()
    {
        $user = Auth::user();
        $nasabah = User::with('alamat')->get();

        $nasabahTerverifikasi = User::where('status', 'Verify')->where('role', 'User')->get();
        $nasabahTidakTerverifikasi = User::where('status', 'Unverifyed')->where('role', 'User')->get();

        return view('admin.nasabah.index', compact('nasabah', 'nasabahTerverifikasi', 'nasabahTidakTerverifikasi'));
    }
    public function addData(Request $request)
    {
        $request->validate([
            "kecamatan" => "required",
            'desa' => 'required',
            "username" => "required",
            // 'alamat_id' => 'required|exists:alamats,id',
            'name' => 'required',
            "email" => "nullable",
            'Nik' => [
                'required',
                'digits:16',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(\d)\1{15}$/', $value)) {
                        session()->flash('swal_error', 'NIK tidak boleh terdiri dari angka yang sama.');
                        return $fail('NIK tidak boleh terdiri dari angka yang sama.');
                    }
                },
            ],
            'no_telp' => 'required|max_digits:12',
            'jenis_kelamin' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ktp' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kk' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'required',
            'tanggal_lahir' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $minAge = 17;
                    $birthDate = Carbon::parse($value);
                    if ($birthDate->diffInYears(Carbon::now()) < $minAge) {
                        $fail('Umur harus minimal 17 tahun.');
                    }
                }
            ],

        ]);

        if ($request->hasFile('foto')) {
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('images', $foto, 'public');
        }

        if ($request->hasFile('ktp')) {
            $ktp = time() . '.' . $request->ktp->extension();
            $request->ktp->storeAs('images', $ktp, 'public');
        }

        if ($request->hasFile('kk')) {
            $kk = time() . '.' . $request->kk->extension();
            $request->kk->storeAs('images', $kk, 'public');
        }

        $tanggal = Carbon::now();
        $tgl = $tanggal->format('d');
        $bln = $tanggal->format('m');
        $thn = $tanggal->format('y');

        $jumlah = User::whereDate('created_at', $tanggal->toDateString())->count();
        $hariIni = str_pad($jumlah + 1, 3, '0', STR_PAD_LEFT);

        $nmr_anggota = "NMR-{$tgl}{$bln}{$thn}-{$hariIni}";

        User::create([
            'username' => $request->username,
            'desa' => $request->desa,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'alamat_id' => $request->alamat_id,
            'name' => $request->name,
            'nmr_anggota' => $nmr_anggota,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
            'foto' => $foto ?? null,
            'ktp' => $ktp ?? null,
            'kk' => $kk ?? null,
        ]);

        return redirect()->back()->with('success', 'pendaftaran anda berhasil, lengkapi pendaftaran selanjutnya di kantor cu saut maju nauli');
    }

    public function updateCheckbox(Request $request, $id)
    {
        $nasabah = User::findOrFail($id);

        if ($request->has('simpanan_wajib')) {
            $nasabah->simpanan_wajib = $request->simpanan_wajib;
        }

        if ($request->has('administrasi')) {
            $nasabah->administrasi = $request->administrasi;
        }

        $nasabah->save();

        return response()->json(['status' => 'success']);
    }


    public function verify($id)
    {
        $nasabah = User::findOrFail($id);

        if (!$nasabah->simpanan_wajib || !$nasabah->administrasi) {
            return redirect()->back()->with('error', 'Tidak bisa verifikasi sebelum semua checkbox dicentang.');
        }

        $lastUser = User::whereNotNull('nm_koperasi')
            ->where('nm_koperasi', 'like', 'AGT-%')
            ->orderByDesc('nm_koperasi')
            ->first();

        if ($lastUser && preg_match('/AGT-(\d+)/', $lastUser->nm_koperasi, $match)) {
            $lastNumber = (int) $match[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $next = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $nmr_anggota = "AGT-{$next}";

        $nasabah->status = 'Verify';
        $nasabah->nm_koperasi = $nmr_anggota;
        $nasabah->save();

        $nasabah->notify(new DataVerify(Auth::user()->name));

        return redirect()->route('nasabah.index')->with("success", "Nasabah berhasil diverifikasi");
    }

    public function edit($id)
    {
        $nasabah = User::findOrfail($id);
        return redirect()->route('nasabah.index', compact('nasabah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable',
            'Nik' => 'nullable',
            'no_telp' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'tanggal_lahir' => 'nullable',
            'foto' => 'nullable',
            'kk' => 'nullable',
            'ktp' => 'nullable',
            'alamat' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'nullable',
        ]);

        $nasabah = User::findOrFail($id);


        if ($request->hasFile('foto')) {
            if ($nasabah->foto) {
                unlink(public_path('images/' . $nasabah->foto));
            }

            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('images'), $foto);
        } else {
            $foto = $nasabah->foto;
        }

        if ($request->hasFile('ktp')) {
            if ($nasabah->ktp) {
                unlink(public_path('images/' . $nasabah->ktp));
            }

            $ktp = time() . '.' . $request->ktp->extension();
            $request->ktp->move(public_path('images'), $ktp);
        } else {
            $ktp = $nasabah->ktp;
        }

        if ($request->hasFile('kk')) {
            if ($nasabah->kk) {
                unlink(public_path('images/' . $nasabah->kk));
            }

            $kk = time() . '.' . $request->kk->extension();
            $request->kk->move(public_path('images'), $kk);
        } else {
            $kk = $nasabah->kk;
        }

        $nasabah->update([
            'name' => $request->name,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
            'foto' => isset($foto) ? $foto : $nasabah->foto,
            'ktp' => isset($ktp) ? $ktp : $nasabah->ktp,
            'kk' => isset($kk) ? $kk : $nasabah->kk,
        ]);

        return redirect()->route('nasabah.index')->with('success', 'Data berhasil diPerbarui!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('nasabah.index')->with('delete', 'Data berhasil diHapus!');
    }
}
