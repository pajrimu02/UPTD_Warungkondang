<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    protected $fillable = ['nama_layanan', 'slug', 'deskripsi', 'icon', 'urutan', 'aktif'];
}
