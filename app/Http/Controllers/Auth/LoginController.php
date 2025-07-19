<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        if (auth()->user()->role == 'Admin'  || auth()->user()->role == 'Kepala') {
            return route('home');
        } else {
            return route('user.edit');
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->status !== 'Verify') {
            auth()->logout();

            return redirect()->back()->with([
                'swal' => [
                    'title' => 'Login Gagal!',
                    'text' => 'Akun Anda belum aktif atau belum diverifikasi.',
                    'icon' => 'error'
                ]
            ])->withInput($request->only('username'));
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user) {
            return redirect()->back()->with([
                'swal' => [
                    'title' => 'Login Gagal!',
                    'text' => 'Username Tidak Ditemukan .',
                    'icon' => 'error'
                ]
            ])->withInput($request->only('username'));
        }

        return redirect()->back()->with([
            'swal' => [
                'title' => 'Login Gagal!',
                'text' => 'Password Salah.',
                'icon' => 'error'
            ]
        ])->withInput($request->only('username'));
    }
}
