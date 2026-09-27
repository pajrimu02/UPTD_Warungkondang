<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;

class EdukasiController extends Controller
{
    public function index()
    {
        $data = Edukasi::latest()->get();

        return view('public.edukasi', compact('data'));
    }

    public function show(string $slug)
    {
        $artikel = Edukasi::where('slug', $slug)->firstOrFail();

        return view('public.edukasi-detail', compact('artikel'));
    }
}
