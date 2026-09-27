<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $fillable = ['judul', 'jenis', 'file_path', 'video_url', 'tanggal'];

    protected $casts = [
        'tanggal' => 'date',
    ];
}