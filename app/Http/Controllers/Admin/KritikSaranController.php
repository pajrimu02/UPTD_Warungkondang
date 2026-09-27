<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function update(Request $request, KritikSaran $kritikSaran): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,ditanggapi'],
            'tanggapan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $kritikSaran->update($validated);

        return redirect()
            ->route('admin.kritiksaran.show', $kritikSaran)
            ->with('success', 'Tanggapan berhasil disimpan.');
    }
}