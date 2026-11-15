<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UndanganController extends Controller
{
    public function show(string $kode): View|RedirectResponse
    {
        $kode = strtoupper(trim($kode));
        $poktan = Poktan::with('desa')->where('kode_undangan', $kode)->first();
        $valid = $poktan && $poktan->status === 'draft' && ! $poktan->dikunci_at;

        // Simpan kode supaya tidak hilang saat pengunjung berpindah antara login dan register
        if ($valid) {
            session(['kode_undangan' => $kode]);
        }

        if ($valid && auth()->check() && auth()->user()->role === 'user') {
            return redirect()->route('user.poktan.gabung', ['kode' => $kode]);
        }

        return view('public.undangan', [
            'poktan' => $valid ? $poktan : null,
            'ketuaNama' => $valid ? $poktan->anggota()->where('jabatan', 'ketua')->value('nama') : null,
            'jumlah' => $valid ? $poktan->anggota()->where('status', '!=', 'nonaktif')->count() : 0,
            'peran' => auth()->check() ? auth()->user()->role : null,
        ]);
    }
}