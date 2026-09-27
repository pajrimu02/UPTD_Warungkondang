<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home');
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
