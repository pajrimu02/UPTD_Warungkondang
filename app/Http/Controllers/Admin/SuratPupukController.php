<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPupuk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuratPupukController extends Controller
{
    public function index(Request $request): View
    {
        $suratPupuks = SuratPupuk::with('poktan', 'user')->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.surat-pupuk.index', compact('suratPupuks'));
    }

    public function show(SuratPupuk $suratPupuk): View
    {
        $suratPupuk->load('user', 'poktan.desa', 'items', 'disetujuiOleh');

        return view('admin.surat-pupuk.show', [
            'suratPupuk' => $suratPupuk,
            'rekap' => $this->rekap($suratPupuk),
        ]);
    }

    public function update(Request $request, SuratPupuk $suratPupuk): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,diproses,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] === 'ditolak' && blank($validated['catatan_admin'] ?? null)) {
            return back()->withErrors(['catatan_admin' => 'Isi alasan penolakan.'])->withInput();
        }

        if ($validated['status'] === 'diterima' && $suratPupuk->status !== 'diterima') {
            $validated['disetujui_oleh'] = auth()->id();
            $validated['disetujui_at'] = now();

            if (! $suratPupuk->nomor_surat) {
                $validated['nomor_surat'] = SuratPupuk::generateNomorSurat();
            }
        }

        $suratPupuk->update($validated);

        return redirect()->route('admin.surat-pupuk.show', $suratPupuk)
            ->with('success', 'Status pengajuan pupuk "' . $suratPupuk->poktan->nama_kelompok . '" berhasil diperbarui.');
    }

    public function lembarKerja(SuratPupuk $suratPupuk)
    {
        $suratPupuk->load('user', 'poktan.desa', 'items');

        $pdf = Pdf::loadView('admin.surat-pupuk.lembar-kerja', [
            'suratPupuk' => $suratPupuk,
            'rekap' => $this->rekap($suratPupuk),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Lembar-Kerja-Pupuk-' . $suratPupuk->id . '.pdf');
    }

    public function uploadSurat(Request $request, SuratPupuk $suratPupuk): RedirectResponse
    {
        $request->validate([
            'file_surat_resmi' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        if ($suratPupuk->file_surat_resmi) {
            Storage::disk('public')->delete($suratPupuk->file_surat_resmi);
        }

        $suratPupuk->update([
            'file_surat_resmi' => $request->file('file_surat_resmi')->store('surat-resmi-pupuk', 'public'),
        ]);

        return redirect()->route('admin.surat-pupuk.show', $suratPupuk)
            ->with('success', 'Surat resmi berhasil diarsipkan.');
    }

    /** Total tiap jenis pupuk per musim tanam: $rekap[musim 0..2][kunci] */
    private function rekap(SuratPupuk $surat): array
    {
        $rekap = [];
        foreach ([0, 1, 2] as $m) {
            foreach (['luas', 'urea', 'sp36', 'za', 'npk', 'organik'] as $k) {
                $rekap[$m][$k] = $surat->items->sum(fn ($it) => (float) ($it->musim[$m][$k] ?? 0));
            }
        }

        return $rekap;
    }
}