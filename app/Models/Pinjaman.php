<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function angsuran()
    {
        return $this->hasMany(Anggsuran::class, 'pinjaman_id');
    }

    public function nasabah()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
