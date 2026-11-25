<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KritikSaranController extends Controller
{
    public function index(Request $request): View
    {
        $kritikSarans = KritikSaran::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.kritik-saran.index', compact('kritikSarans'));
    }

    public function show(KritikSaran $kritikSaran): View
    {
        $kritikSaran->load('user');

        return view('admin.kritik-saran.show', compact('kritikSaran'));
    }

    public function kelola(KritikSaran $kritikSaran): View
    {
        $kritikSaran->load('user');

        return view('admin.kritik-saran.kelola', compact('kritikSaran'));
    }

    public function update(Request $request, KritikSaran $kritikSaran): RedirectResponse
    {
        $validated = $request->validate([
            'tanggapan_admin' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'tanggapan_admin.required' => 'Tanggapan wajib diisi sebelum dikirim.',
        ]);

        $kritikSaran->update([
            'tanggapan_admin' => $validated['tanggapan_admin'],
            'status' => 'ditanggapi',
        ]);

        return redirect()
            ->route('admin.kritiksaran.show', $kritikSaran)
            ->with('success', 'Tanggapan berhasil disimpan dan status otomatis berubah menjadi Ditanggapi.');
    }

    public function destroy(KritikSaran $kritikSaran): RedirectResponse
    {
        $kritikSaran->delete();

        return redirect()
            ->route('admin.kritiksaran.index')
            ->with('success', 'Kritik & saran berhasil dihapus.');
    }
}