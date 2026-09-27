<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
{
    $daftarLayanan = \App\Models\Pelayanan::where('aktif', true)->orderBy('urutan')->get();
    $infoTerbaru = \App\Models\StokKuota::latest()->get();
    $edukasiTerbaru = \App\Models\Edukasi::latest()->get();

    return view('public.home', compact('daftarLayanan', 'infoTerbaru', 'edukasiTerbaru'));
}

    public function tentangKami()
    {
        return view('public.tentang');
    }

    public function hubungiKami()
    {
        return view('public.hubungi');
    }
}
