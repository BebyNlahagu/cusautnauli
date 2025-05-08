<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'simpanans';
    protected $guarded = [];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, "nasabah_id");
    }
}
