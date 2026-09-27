<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Galeri;

class GaleriController extends Controller
{
    public function foto()
    {
        $data = Galeri::where('jenis', 'foto')->latest()->get();

        return view('public.galeri-foto', compact('data'));
    }

    public function video()
    {
        $data = Galeri::where('jenis', 'video')->latest()->get();

        return view('public.galeri-video', compact('data'));
    }
}
