<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggsuran extends Model
{
    use HasFactory;

    protected $table = "angsuran";
    protected $guarded = [];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, "pinjaman_id");
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
}
