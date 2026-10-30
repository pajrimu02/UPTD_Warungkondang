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
        'status' => ['required', 'in:diajukan,diproses,ditolak,diterima'],
        'catatan_admin' => ['nullable', 'string', 'max:1000'],
        'alokasi_volume' => ['nullable', 'numeric', 'min:0'],
        'tempat_pengambilan' => ['nullable', 'string', 'max:255'],
        'nomor_penyalur' => ['nullable', 'string', 'max:100'],
        'alamat_penyalur' => ['nullable', 'string', 'max:500'],
        'alat_pembelian' => ['nullable', 'string', 'max:100'],
        'berlaku_sampai' => ['nullable', 'date'],
    ]);

    if ($validated['status'] === 'diterima' && ! $suratSolar->diserahkan_at) {
        return back()->withErrors(['status' => 'Status "Diterima" terisi otomatis saat surat dikirim ke petani.'])->withInput();
    }

    if ($validated['status'] === 'ditolak' && blank($validated['catatan_admin'] ?? null)) {
        return back()->withErrors(['catatan_admin' => 'Isi alasan penolakan.'])->withInput();
    }

    // "Diproses" = admin menyetujui dan menyiapkan surat
    if ($validated['status'] === 'diproses') {
        foreach ($suratSolar->isianOtomatis() as $kolom => $nilai) {
            if (blank($validated[$kolom] ?? null)) {
                $validated[$kolom] = $suratSolar->$kolom ?? $nilai;
            }
        }

        if (blank($validated['nomor_penyalur']) || blank($validated['alamat_penyalur'])) {
            return back()->withErrors(['nomor_penyalur' => 'Nomor dan alamat penyalur belum ada.'])->withInput();
        }

        if (! $suratSolar->nomor_surat) {
            $validated['disetujui_oleh'] = auth()->id();
            $validated['disetujui_at'] = now();
            $validated['nomor_surat'] = SuratSolar::generateNomorSurat();
        }
    }

    $suratSolar->update($validated);

    return redirect()
        ->route('admin.surat-solar.kelola', $suratSolar)
        ->with('success', 'Keputusan pengajuan "' . ($suratSolar->nama_pemohon ?? $suratSolar->user->name) . '" disimpan.');
}

        public function lembarKerja(SuratSolar $suratSolar)
        {
            $suratSolar->load('user', 'poktan');

            return Pdf::loadView('pdf.surat-rekomendasi-solar', compact('suratSolar'))
                ->setPaper('a4')
                ->stream('Surat-Rekomendasi-' . $suratSolar->id . '.pdf');
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
                'ttd_kembali_at' => $suratSolar->ttd_kembali_at ?? now(), // cadangan jika ketua tidak mengunggah sendiri
            ]);

            return redirect()->route('admin.surat-solar.kelola', $suratSolar)
                ->with('success', 'Scan surat berhasil diarsipkan.');
        }

        public function kelola(SuratSolar $suratSolar): View
        {
            $suratSolar->load('user', 'poktan', 'disetujuiOleh');

            return view('admin.surat-solar.kelola', [
                'suratSolar' => $suratSolar,
                'isian' => $suratSolar->isianOtomatis(),
            ]);
        }

       public function tahap(Request $request, SuratSolar $suratSolar): RedirectResponse
{
    $aksi = $request->validate([
        'aksi' => ['required', 'in:kirim_ketua,serahkan'],
    ])['aksi'];

    $s = $suratSolar;

    if ($aksi === 'kirim_ketua') {
        if (! in_array($s->status, ['diproses', 'diterima'], true) || ! $s->nomor_surat || blank($s->alokasi_volume)) {
            return back()->with('error', 'Surat belum lengkap. Pilih status Diproses dan simpan keputusan dulu.');
        }

        $s->update(['ttd_dikirim_at' => now()]);

        return back()->with('success', 'Surat dikirim ke ketua untuk ditandatangani.');
    }

    // serahkan: kirim ke petani, status otomatis menjadi Diterima
    if (! $s->ttd_kembali_at || ! $s->file_surat_resmi) {
        return back()->with('error', 'Scan surat bertanda tangan belum ada.');
    }

    $s->update(['status' => 'diterima', 'diserahkan_at' => now()]);

    return back()->with('success', 'Surat dikirim ke petani. Status pengajuan kini Diterima.');
}

        public function destroy(SuratSolar $suratSolar): RedirectResponse
        {
            $nama = $suratSolar->nama_pemohon ?? $suratSolar->user->name;

            Storage::disk('public')->delete(array_filter([
                $suratSolar->file_ktp, $suratSolar->file_sku,
                $suratSolar->file_foto_mesin, $suratSolar->file_surat_resmi,
            ]));

            $suratSolar->delete();

            return redirect()->route('admin.surat-solar.index')
                ->with('success', 'Pengajuan "' . $nama . '" dihapus.');
        }
}