<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $table = "alamats";
    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(User::class, "alamat_id");
    }
}
