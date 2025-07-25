<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function petugas()
    {
        return $this->hasMany(Petugas::class, 'user_id');
    }

    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'user_id');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'user_id');
    }

    public function angsuran()
    {
        return $this->hasMany(Anggsuran::class, 'user_id');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    public function simpans()
    {
        return $this->hasMany(Simpan::class,'user_id');
    }
}
