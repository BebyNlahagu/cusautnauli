<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpan extends Model
{
    use HasFactory;

    protected $table = 'simpans';
    protected $guarded = [];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(Simpan::class,'user_id');
    }
}
