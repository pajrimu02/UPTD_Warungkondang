<?php
// app/Http/Controllers/User/SuratSolarController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuratSolarController extends Controller
{
    public function index(): View
    {
        $suratSolars = SuratSolar::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.surat-solar.index', compact('suratSolars'));
    }

    public function create(): View
    {
        return view('user.surat-solar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelompok_tani' => ['nullable', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'alamat' => ['required', 'string', 'max:255'],
            'luas_lahan' => ['required', 'numeric', 'min:0.01'],
            'jumlah_liter_diajukan' => ['required', 'integer', 'min:1'],
            'keperluan' => ['required', 'string', 'max:1000'],
            'file_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('file_pendukung')) {
            $validated['file_pendukung'] = $request->file('file_pendukung')
                ->store('surat-solar', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        SuratSolar::create($validated);

        return redirect()
            ->route('user.surat-solar.index')
            ->with('status', 'Pengajuan berhasil dikirim. Menunggu diproses.');
    }

    public function show(SuratSolar $suratSolar): View
    {
        abort_unless($suratSolar->user_id === auth()->id(), 403);

        return view('user.surat-solar.show', compact('suratSolar'));
    }
}