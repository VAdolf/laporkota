<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_WARGA = 'warga';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EKSEKUTIF = 'eksekutif';

    /**
     * Nilai bawaan atribut (sama dengan default pada migrasi),
     * agar $user->role tidak null tepat setelah User::create().
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_WARGA,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * 'role' ditambahkan agar seeder (admin/eksekutif) tersimpan dengan benar.
     * Jangan pernah meneruskan $request->all() ke User::create()/fill().
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
