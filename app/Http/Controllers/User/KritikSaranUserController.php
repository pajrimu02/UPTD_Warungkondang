<?php
// app/Http/Controllers/User/KritikSaranUserController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KritikSaranUserController extends Controller
{
    public function create(): View
    {
        $riwayat = KritikSaran::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.kritik-saran.create', compact('riwayat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori' => ['required', 'in:surat,pupuk_solar,lainnya'],
            'isi' => ['required', 'string', 'max:2000'],
        ]);

        $validated['user_id'] = auth()->id();

        KritikSaran::create($validated);

        return redirect()
            ->route('user.kritiksaran.create')
            ->with('status', 'Terima kasih, masukan kamu sudah terkirim.');
    }
}