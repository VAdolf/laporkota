<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tanggapan extends Model
{
    protected $fillable = [
        'laporan_id',
        'user_id',
        'status',
        'catatan',
        'foto_hasil',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    /** Petugas/admin yang menanggapi. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFotoHasilUrlAttribute(): ?string
    {
        return $this->foto_hasil ? asset('storage/'.$this->foto_hasil) : null;
    }
}
