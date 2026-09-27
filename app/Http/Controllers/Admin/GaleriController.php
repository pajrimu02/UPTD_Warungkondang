<?php
// app/Http/Controllers/Admin/GaleriController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(Request $request): View
    {
        $galeris = Galeri::when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.galeri.index', compact('galeris'));
    }

    public function create(): View
    {
        return view('admin.galeri.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:foto,video'],
            'file_path' => ['required_if:jenis,foto', 'nullable', 'image', 'max:4096'],
            'video_url' => ['required_if:jenis,video', 'nullable', 'url'],
            'tanggal' => ['nullable', 'date'],
        ]);

        if ($request->jenis === 'foto' && $request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('galeri', 'public');
        } else {
            $validated['file_path'] = null;
        }

        Galeri::create($validated);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function edit(Galeri $galeri): View
    {
        return view('admin.galeri.edit', compact('galeri'));
    }

    public function update(Request $request, Galeri $galeri): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:foto,video'],
            'file_path' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['required_if:jenis,video', 'nullable', 'url'],
            'tanggal' => ['nullable', 'date'],
        ]);

        if ($request->jenis === 'foto' && $request->hasFile('file_path')) {
            if ($galeri->file_path) {
                Storage::disk('public')->delete($galeri->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('galeri', 'public');
        } else {
            unset($validated['file_path']);
        }

        $galeri->update($validated);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri): RedirectResponse
    {
        if ($galeri->file_path) {
            Storage::disk('public')->delete($galeri->file_path);
        }

        $galeri->delete();

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Item galeri berhasil dihapus.');
    }
}