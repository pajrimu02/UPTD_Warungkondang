<?php
// app/Http/Controllers/Admin/SuratSolarController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuratSolarController extends Controller
{
    public function index(Request $request): View
    {
        $suratSolars = SuratSolar::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.surat-solar.index', compact('suratSolars'));
    }

    public function show(SuratSolar $suratSolar): View
    {
        $suratSolar->load('user');

        return view('admin.surat-solar.show', compact('suratSolar'));
    }

    public function update(Request $request, SuratSolar $suratSolar): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,diproses,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
            'nomor_surat' => ['nullable', 'string', 'max:255'],
        ]);

        $suratSolar->update($validated);

        return redirect()
            ->route('admin.surat-solar.show', $suratSolar)
            ->with('status', 'Status pengajuan berhasil diperbarui.');
    }
}