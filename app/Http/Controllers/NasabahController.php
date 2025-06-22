<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
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
        $nasabah = Nasabah::all();

        $nasabahTerverifikasi = Nasabah::where("status","Verify")->get();
        $nasabahTidakTerverifikasi = Nasabah::where("status","Unverifyed")->get();
        return view('admin.nasabah.index', compact('nasabah','nasabahTerverifikasi','nasabahTidakTerverifikasi'));
    }

    public function addData(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'Nik' => 'required|max_digits:16',
            'no_telp' => 'required|max_digits:12',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ktp'  => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kk'   => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'required',
            'tanggal_lahir' => ['required', 'date', function ($attribute, $value, $fail) {
                $minAge = 17;
                $birthDate = Carbon::parse($value);
                if ($birthDate->diffInYears(Carbon::now()) < $minAge) {
                    $fail('Umur harus minimal 17 tahun.');
                }
            }],
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

        $jumlah = Nasabah::whereDate('created_at', $tanggal->toDateString())->count();
        $hariIni = str_pad($jumlah + 1, 3, '0', STR_PAD_LEFT);

        $nmr_anggota = "NMR-{$tgl}{$bln}{$thn}-{$hariIni}";

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "role" => "User",
            "password" => Hash::make($request->password),
        ]);

        $nasabah = Nasabah::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'nmr_anggota' => $nmr_anggota,
            'Nik' => $request->Nik,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'pekerjaan' => $request->pekerjaan,
            'foto' => $foto ?? null,
            'ktp' => $ktp ?? null,
            'kk' => $kk ?? null,
        ]);

        $user->nasabah_id = $nasabah->id;
        $user->save();

        return redirect()->back()->with('success', 'Nasabah berhasil didaftarkan!');
    }

    public function verify($id)
    {
        $nasabah = Nasabah::with('user')->findOrFail($id);

        $nasabah->status = 'Verify';
        $nasabah->save();
        return redirect()->route('nasabah.index')->with("success", "Nasabah berhasil diverifikasi");
    }

    public function edit($id)
    {
        $nasabah = Nasabah::findOrfail($id);
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
            // 'tanggal_masuk' => 'nullable',
            'alamat' => 'nullable',
            'kelurahan' => 'nullable',
            'pekerjaan' => 'nullable',
        ]);

        $nasabah = Nasabah::findOrFail($id);


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
            // 'tanggal_masuk' => $request->tanggal_masuk,
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
        Nasabah::findOrFail($id)->delete();

        return redirect()->route('nasabah.index')->with('delete', 'Data berhasil diHapus!');
    }
}
