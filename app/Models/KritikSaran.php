<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KritikSaran extends Model
{
    protected $table = 'kritik_sarans';
    protected $fillable = ['user_id', 'kategori', 'isi', 'status', 'tanggapan_admin'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
