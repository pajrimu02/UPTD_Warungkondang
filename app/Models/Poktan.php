<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poktan extends Model
{
    protected $fillable = ['nama_kelompok', 'desa', 'kecamatan'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}