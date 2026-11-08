<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    /** Urutan sama dengan enum pada migrasi laporans. */
    public const STATUS = [
        self::STATUS_MENUNGGU,
        self::STATUS_DIPROSES,
        self::STATUS_SELESAI,
        self::STATUS_DITOLAK,
    ];

    protected $fillable = [
        'user_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'lat',
        'lng',
        'foto_bukti',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return asset('storage/'.$this->foto_bukti);
    }

    /** Koordinat 0,0 (data lama dari form sebelumnya) dianggap belum ada lokasi. */
    public function hasLocation(): bool
    {
        return $this->lat !== null
            && $this->lng !== null
            && ! ($this->lat == 0 && $this->lng == 0);
    }
}
