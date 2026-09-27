<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokKuota extends Model
{
    protected $table = 'stok_kuotas';
    protected $fillable = ['jenis', 'judul', 'isi', 'periode'];
}
