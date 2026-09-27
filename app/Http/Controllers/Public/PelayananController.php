<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pelayanan;

class PelayananController extends Controller
{
    public function index()
    {
        $daftarLayanan = Pelayanan::where('aktif', true)->orderBy('urutan')->get();

        return view('public.pelayanan', compact('daftarLayanan'));
    }
}
