<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Informasi;

class InformasiController extends Controller
{
    public function index()
    {
        $data = Informasi::latest()->get();

        return view('public.informasi', compact('data'));
    }
}
