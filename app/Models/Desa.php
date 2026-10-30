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

    // Luas yang sudah dipakai poktan berstatus DISETUJUI di desa ini
    public function terpakaiHa(): float
    {
        return (float) Poktan::where('desa_id', $this->id)
            ->where('status', 'disetujui')
            ->sum('luas_lahan_ha');
    }

    public function sisaHa(): float
    {
        return max(0, (float) $this->luas_baku_sawah_ha - $this->terpakaiHa());
    }
}