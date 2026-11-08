<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori'];

    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
