<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = ['nama', 'kode_desa', 'luas_baku_sawah_ha'];

    public function poktans()
    {
        return $this->hasMany(Poktan::class);
    }

    /** Batas luas desa: LBS resmi jika sudah diisi, jika belum pakai batas bawaan, jika tidak ada keduanya null. */
    public function batasHa(): ?float
    {
        $lbs = (float) $this->luas_baku_sawah_ha;
        if ($lbs > 0) {
            return $lbs;
        }

        $bawaan = (float) config('uptd.default_lbs_desa_ha', 0);

        return $bawaan > 0 ? $bawaan : null;
    }

    /** True jika yang dipakai masih batas bawaan, bukan LBS resmi. */
    public function memakaiBatasBawaan(): bool
    {
        return (float) $this->luas_baku_sawah_ha <= 0 && $this->batasHa() !== null;
    }

    // Luas yang sudah dipakai poktan berstatus DISETUJUI di desa ini
    public function terpakaiHa(): float
    {
        return (float) Poktan::where('desa_id', $this->id)
            ->where('status', 'disetujui')
            ->sum('luas_lahan_ha');
    }

    public function sisaHa(): float
    {
        return max(0, ($this->batasHa() ?? 0) - $this->terpakaiHa());
    }
}