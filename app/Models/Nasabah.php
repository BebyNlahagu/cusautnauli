<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';
    protected $guarded = [];

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, "nasabah_id");
    }

    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, "nasabah_id");
    }
    public function angsuran()
    {
        return $this->hasMany(Anggsuran::class, "nasabah_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
