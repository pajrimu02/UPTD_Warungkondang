<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
        $suratSolar->load('user', 'poktan', 'disetujuiOleh');

        return view('admin.surat-solar.show', compact('suratSolar'));
    }

    public function update(Request $request, SuratSolar $suratSolar): RedirectResponse
{
    $validated = $request->validate([
        'status' => ['required', 'in:diajukan,diproses,diterima,ditolak'],
        'catatan_admin' => ['nullable', 'string', 'max:1000'],
    ]);

    if ($validated['status'] === 'diterima' && $suratSolar->status !== 'diterima') {
        $validated['disetujui_oleh'] = auth()->id();
        $validated['disetujui_at'] = now();

        if (! $suratSolar->nomor_surat) {
            $validated['nomor_surat'] = SuratSolar::generateNomorSurat();
        }
    }

    $suratSolar->update($validated);

    return redirect()
        ->route('admin.surat-solar.show', $suratSolar)
        ->with('success', 'Status pengajuan "'.($suratSolar->nama_pemohon ?? $suratSolar->user->name).'" berhasil diperbarui.');
}

    public function lembarKerja(SuratSolar $suratSolar)
    {
        $suratSolar->load('user', 'poktan');

        $pdf = Pdf::loadView('admin.surat-solar.lembar-kerja', compact('suratSolar'));

        return $pdf->stream('Lembar-Kerja-'.$suratSolar->id.'.pdf');
    }

    public function uploadSurat(Request $request, SuratSolar $suratSolar): RedirectResponse
    {
        $request->validate([
            'file_surat_resmi' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        if ($suratSolar->file_surat_resmi) {
            Storage::disk('public')->delete($suratSolar->file_surat_resmi);
        }

        $suratSolar->update([
            'file_surat_resmi' => $request->file('file_surat_resmi')->store('surat-resmi', 'public'),
        ]);

        return redirect()
            ->route('admin.surat-solar.show', $suratSolar)
            ->with('success', 'Surat resmi berhasil diarsipkan.');
    }
}