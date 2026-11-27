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
    private const F4 = [0, 0, 609.45, 935.43];

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

    public function kelola(SuratPupuk $suratPupuk): View
    {
        $suratPupuk->load('user', 'poktan.desa', 'disetujuiOleh')->loadCount('items');

        return view('admin.surat-pupuk.kelola', compact('suratPupuk'));
    }

    public function update(Request $request, SuratPupuk $suratPupuk): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,diproses,ditolak,diterima'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] === 'diterima' && ! $suratPupuk->diserahkan_at) {
            return back()->withErrors(['status' => 'Status "Diterima" terisi otomatis saat surat dikirim ke petani.'])->withInput();
        }

        if ($validated['status'] === 'ditolak' && blank($validated['catatan_admin'] ?? null)) {
            return back()->withErrors(['catatan_admin' => 'Isi alasan penolakan.'])->withInput();
        }

        $pesan = 'Keputusan pengajuan pupuk "' . $suratPupuk->poktan->nama_kelompok . '" disimpan.';

        if ($validated['status'] === 'diproses') {
            if (! $suratPupuk->nomor_surat) {
                $validated['disetujui_oleh'] = auth()->id();
                $validated['disetujui_at'] = now();
                $validated['nomor_surat'] = SuratPupuk::generateNomorSurat();
            }
            if (! $suratPupuk->ttd_dikirim_at) {
                $validated['ttd_dikirim_at'] = now();
                $pesan = 'Pengajuan disetujui, nomor surat terbit, dan langsung diteruskan ke Kasubbag TU untuk ditandatangani.';
            }
        }

        $suratPupuk->update($validated);

        return redirect()->route('admin.surat-pupuk.kelola', $suratPupuk)->with('success', $pesan);
    }

    public function surat(SuratPupuk $suratPupuk)
    {
        $suratPupuk->load('poktan.desa', 'items');

        return Pdf::loadView('pdf.surat-rekomendasi-pupuk', compact('suratPupuk'))
            ->setPaper(self::F4, 'landscape')
            ->stream('Surat-Rekomendasi-Pupuk-' . $suratPupuk->id . '.pdf');
    }

    private function rekap(SuratPupuk $surat): array
    {
        $rekap = [];
        foreach ([0, 1, 2] as $m) {
            foreach (['luas', 'urea', 'npk', 'npk_formula', 'organik', 'za'] as $k) {
                $rekap[$m][$k] = $surat->items->sum(fn ($it) => (float) ($it->musim[$m][$k] ?? 0));
            }
        }

        return $rekap;
    }

    public function tahap(Request $request, SuratPupuk $suratPupuk): RedirectResponse
    {
        $aksi = $request->validate([
            'aksi' => ['required', 'in:kirim_ketua,serahkan'],
        ])['aksi'];

        $s = $suratPupuk;

        if ($aksi === 'kirim_ketua') {
            if (! in_array($s->status, ['diproses', 'diterima'], true) || ! $s->nomor_surat) {
                return back()->with('error', 'Pilih status Diproses dan simpan keputusan dulu.');
            }

            $s->update(['ttd_dikirim_at' => now()]);

            return back()->with('success', 'Surat dikirim ke ketua untuk ditandatangani.');
        }

        if (! $s->ttd_kembali_at || ! $s->file_surat_resmi) {
            return back()->with('error', 'Scan surat bertanda tangan belum ada.');
        }

        $s->update(['status' => 'diterima', 'diserahkan_at' => now()]);

        return back()->with('success', 'Surat dikirim ke petani. Status pengajuan kini Diterima.');
    }

   
    public function lembarKerja(SuratPupuk $suratPupuk)
    {
        $suratPupuk->load('user', 'poktan.desa', 'items');

        return Pdf::loadView('admin.surat-pupuk.lembar-kerja', [
            'suratPupuk' => $suratPupuk,
            'rekap' => $this->rekap($suratPupuk),
        ])->setPaper('a4', 'landscape')->stream('Lembar-Kerja-Pupuk-' . $suratPupuk->id . '.pdf');
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
            'ttd_kembali_at' => $suratPupuk->ttd_kembali_at ?? now(), // cadangan jika ketua tidak mengunggah sendiri
        ]);

        return redirect()->route('admin.surat-pupuk.kelola', $suratPupuk)
            ->with('success', 'Scan surat berhasil diarsipkan.');
    }

    public function destroy(SuratPupuk $suratPupuk): RedirectResponse
    {
        $nama = $suratPupuk->poktan->nama_kelompok;

        if ($suratPupuk->file_surat_resmi) {
            Storage::disk('public')->delete($suratPupuk->file_surat_resmi);
        }

        $suratPupuk->delete();

        return redirect()->route('admin.surat-pupuk.index')
            ->with('success', 'Pengajuan pupuk "' . $nama . '" dihapus.');
    }

}