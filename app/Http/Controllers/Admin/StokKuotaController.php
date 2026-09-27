<?php
// app/Http/Controllers/Admin/StokKuotaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StokKuota;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StokKuotaController extends Controller
{
    public function index(Request $request): View
    {
        $stokKuotas = StokKuota::when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stok-kuota.index', compact('stokKuotas'));
    }

    public function create(): View
    {
        return view('admin.stok-kuota.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pupuk,solar'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'periode' => ['nullable', 'string', 'max:255'],
        ]);

        StokKuota::create($validated);

        return redirect()
            ->route('admin.stok-kuota.index')
            ->with('success', 'Informasi stok & kuota berhasil ditambahkan.');
    }

    public function edit(StokKuota $stokKuota): View
    {
        return view('admin.stok-kuota.edit', compact('stokKuota'));
    }

    public function update(Request $request, StokKuota $stokKuota): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pupuk,solar'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'periode' => ['nullable', 'string', 'max:255'],
        ]);

        $stokKuota->update($validated);

        return redirect()
            ->route('admin.stok-kuota.index')
            ->with('success', 'Informasi stok & kuota berhasil diperbarui.');
    }

    public function destroy(StokKuota $stokKuota): RedirectResponse
    {
        $stokKuota->delete();

        return redirect()
            ->route('admin.stok-kuota.index')
            ->with('success', 'Informasi stok & kuota berhasil dihapus.');
    }
}