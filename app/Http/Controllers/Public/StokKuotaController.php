<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StokKuota;

class StokKuotaController extends Controller
{
    public function index()
    {
        $data = StokKuota::latest()->get();

        return view('public.stok-kuota', compact('data'));
    }
}
