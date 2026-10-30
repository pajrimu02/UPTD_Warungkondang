<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
        'no_hp',
        'alamat',
        'konsumen_pengguna',
        'jenis_usaha',
        'nama_kapal',
        'poktan_id',
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

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }

    public function suratSolars(): HasMany
    {
        return $this->hasMany(SuratSolar::class);
    }

         
    public function suratPupuks(): HasMany
    {
        return $this->hasMany(SuratPupuk::class);
    }
    
    public function poktanDisetujui(): ?Poktan
    {
        $p = $this->poktan_id
            ? Poktan::with('desa')->where('status', 'disetujui')->find($this->poktan_id)
            : null;

        return $p ?? $this->poktanPengelola();
    }
    
    public function poktanPengelola(): ?Poktan
    {
        return Poktan::with('desa')
            ->where('status', 'disetujui')
            ->where(function ($q) {
                $q->where('diajukan_oleh', $this->id);
                if ($this->nik) {
                    $q->orWhereHas('anggota', fn ($a) => $a->where('jabatan', 'ketua')->where('nik', $this->nik));
                }
            })
            ->latest()->first();
    }
}