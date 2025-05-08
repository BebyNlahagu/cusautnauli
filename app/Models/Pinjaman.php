<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = "pinjaman";
    protected $guarded = [];

    public function nasabah()
    {
        return  $this->belongsTo(Nasabah::class, "nasabah_id");
    }

    public function angsuran()
    {
        return $this->hasMany(Anggsuran::class,'pinjaman_id');
    }
}
