<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;

class KritikSaranController extends Controller
{
    // Publik: hanya baca (read-only), tidak ada form submit di sini
    public function index()
    {
        $data = KritikSaran::where('status', 'ditanggapi')->latest()->get();

        return view('public.kritik-saran', compact('data'));
    }
}
