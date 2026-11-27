<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuratController extends Controller
{
    private const F4 = [0, 0, 609.45, 935.43];

    private const PESAN_PDF = [
        'file_surat_resmi.mimes' => 'File harus berformat PDF.',
        'file_surat_resmi.max' => 'Ukuran file maksimal 4 MB.',
    ];

    private function adalahKepala(): bool
    {
        return auth()->user()?->role === 'kepala';
    }

    public function index(Request $request): View
    {
        $isKepala = $this->adalahKepala();
        $jenis = ! $isKepala && in_array($request->input('jenis'), ['solar', 'pupuk'], true)
            ? $request->input('jenis')
            : null;

        return view('ketua.surat.index', [
            'menunggu' => $this->daftar(false, $jenis),
            'selesai' => $this->daftar(true, $jenis)->take(10),
            'jenis' => $jenis,
            'isKepala' => $isKepala,
        ]);
    }

    private function daftar(bool $selesai, ?string $jenis = null): Collection
    {
        $kolom = $selesai ? 'ttd_kembali_at' : 'ttd_dikirim_at';

        $filter = fn ($q) => $selesai
            ? $q->whereNotNull('ttd_kembali_at')
            : $q->whereNotNull('ttd_dikirim_at')->whereNull('ttd_kembali_at');

        // KEPALA UPTD: Hanya SK Pengukuhan Poktan
        if ($this->adalahKepala()) {
            return $filter(Poktan::with('desa')->withCount('anggota'))->get()->map(fn ($p) => [
                'jenis' => 'SK Poktan',
                'kunci' => 'poktan',
                'id' => $p->id,
                'ikon' => 'bi-diagram-3',
                'judul' => $p->nama_kelompok,
                'nomor' => $p->nomor_sk,
                'info' => 'Desa ' . ($p->desa?->nama ?? '-') . ' · ' . $p->anggota_count . ' anggota',
                'waktu' => $p->$kolom,
                'terkirim' => $p->diserahkan_at,
                'cetak' => route('ketua.surat.cetak-poktan', $p),
                'upload' => route('ketua.surat.upload-poktan', $p),
            ])->sortBy($selesai ? fn ($x) => -$x['waktu']->timestamp : fn ($x) => $x['waktu']->timestamp)->values();
        }

        // KETUA (SUB BAG TU): Hanya Solar & Pupuk
        $solar = $jenis === 'pupuk' ? collect() : $filter(SuratSolar::with('user', 'poktan'))->get()->map(fn ($s) => [
            'jenis' => 'Solar',
            'kunci' => 'solar',
            'id' => $s->id,
            'ikon' => 'bi-fuel-pump',
            'judul' => $s->nama_pemohon ?? $s->user->name,
            'nomor' => $s->nomor_surat,
            'info' => number_format((float) $s->alokasi_volume, 0, ',', '.') . ' L/minggu · ' . ($s->poktan?->nama_kelompok ?? 'Mandiri'),
            'waktu' => $s->$kolom,
            'terkirim' => $s->diserahkan_at,
            'cetak' => route('ketua.surat.cetak-solar', $s),
            'upload' => route('ketua.surat.upload-solar', $s),
        ]);

        $pupuk = $jenis === 'solar' ? collect() : $filter(SuratPupuk::with('poktan')->withCount('items'))->get()->map(fn ($s) => [
            'jenis' => 'Pupuk',
            'kunci' => 'pupuk',
            'id' => $s->id,
            'ikon' => 'bi-flower2',
            'judul' => $s->poktan->nama_kelompok,
            'nomor' => $s->nomor_surat,
            'info' => $s->items_count . ' petani',
            'waktu' => $s->$kolom,
            'terkirim' => $s->diserahkan_at,
            'cetak' => route('ketua.surat.cetak-pupuk', $s),
            'upload' => route('ketua.surat.upload-pupuk', $s),
        ]);

        $gabung = $solar->concat($pupuk);

        return $selesai ? $gabung->sortByDesc('waktu')->values() : $gabung->sortBy('waktu')->values();
    }

    private function cari(string $jenis, int $id)
    {
        if ($jenis === 'poktan') {
            abort_unless($this->adalahKepala(), 403);
            return Poktan::findOrFail($id);
        }

        abort_if($this->adalahKepala(), 403);
        $model = $jenis === 'solar' ? SuratSolar::class : SuratPupuk::class;

        return $model::findOrFail($id);
    }

    public function scan(string $jenis, int $id)
    {
        $item = $this->cari($jenis, $id);
        $path = $jenis === 'poktan' ? $item->file_sk_resmi : $item->file_surat_resmi;

        abort_unless($path && Storage::disk('public')->exists($path), 404, 'File tidak ditemukan.');

        return Storage::disk('public')->response($path);
    }

    public function hapusScan(string $jenis, int $id): RedirectResponse
    {
        $item = $this->cari($jenis, $id);
        abort_unless($item->ttd_kembali_at && ! $item->diserahkan_at, 403);

        $kolomFile = $jenis === 'poktan' ? 'file_sk_resmi' : 'file_surat_resmi';
        $nomor = $jenis === 'poktan' ? $item->nomor_sk : $item->nomor_surat;

        if ($item->$kolomFile) {
            Storage::disk('public')->delete($item->$kolomFile);
        }

        $item->update([$kolomFile => null, 'ttd_kembali_at' => null]);

        return back()->with('success', 'Scan ' . $nomor . ' dihapus. Dokumen kembali ke daftar menunggu.');
    }

    // ---- Solar & Pupuk (role:ketua) ----

    public function cetakSolar(SuratSolar $suratSolar)
    {
        abort_unless(! $this->adalahKepala() && $suratSolar->ttd_dikirim_at, 403);
        $suratSolar->load('user', 'poktan');

        return Pdf::loadView('pdf.surat-rekomendasi-solar', compact('suratSolar'))
            ->setPaper(self::F4)->stream('Surat-Rekomendasi-' . $suratSolar->id . '.pdf');
    }

    public function uploadSolar(Request $request, SuratSolar $suratSolar): RedirectResponse
    {
        abort_unless(! $this->adalahKepala() && $suratSolar->ttd_dikirim_at && ! $suratSolar->ttd_kembali_at, 403);
        $request->validate(['file_surat_resmi' => ['required', 'file', 'mimes:pdf', 'max:4096']], self::PESAN_PDF);

        $suratSolar->update([
            'file_surat_resmi' => $request->file('file_surat_resmi')->store('surat-resmi', 'public'),
            'ttd_kembali_at' => now(),
        ]);

        return back()->with('success', 'Scan surat ' . $suratSolar->nomor_surat . ' terkirim ke admin.');
    }

    public function cetakPupuk(SuratPupuk $suratPupuk)
    {
        abort_unless(! $this->adalahKepala() && $suratPupuk->ttd_dikirim_at, 403);
        $suratPupuk->load('poktan.desa', 'items');

        return Pdf::loadView('pdf.surat-rekomendasi-pupuk', compact('suratPupuk'))
            ->setPaper(self::F4, 'landscape')
            ->stream('Surat-Rekomendasi-Pupuk-' . $suratPupuk->id . '.pdf');
    }

    public function uploadPupuk(Request $request, SuratPupuk $suratPupuk): RedirectResponse
    {
        abort_unless(! $this->adalahKepala() && $suratPupuk->ttd_dikirim_at && ! $suratPupuk->ttd_kembali_at, 403);
        $request->validate(['file_surat_resmi' => ['required', 'file', 'mimes:pdf', 'max:4096']], self::PESAN_PDF);

        $suratPupuk->update([
            'file_surat_resmi' => $request->file('file_surat_resmi')->store('surat-resmi-pupuk', 'public'),
            'ttd_kembali_at' => now(),
        ]);

        return back()->with('success', 'Scan surat ' . $suratPupuk->nomor_surat . ' terkirim ke admin.');
    }

    // ---- SK Poktan (role:kepala) ----

    public function cetakPoktan(Poktan $poktan)
    {
        abort_unless($this->adalahKepala() && $poktan->ttd_dikirim_at, 403);
        $poktan->load(['desa', 'anggota' => fn ($q) => $q->orderBy('nama')]);

        return Pdf::loadView('pdf.sk-pengukuhan-poktan', ['poktan' => $poktan, 'draf' => false])
            ->setPaper(self::F4)->stream('SK-Pengukuhan-' . $poktan->id . '.pdf');
    }

    public function uploadPoktan(Request $request, Poktan $poktan): RedirectResponse
    {
        abort_unless($this->adalahKepala() && $poktan->ttd_dikirim_at && ! $poktan->ttd_kembali_at, 403);
        $request->validate(['file_surat_resmi' => ['required', 'file', 'mimes:pdf', 'max:4096']], self::PESAN_PDF);

        $poktan->update([
            'file_sk_resmi' => $request->file('file_surat_resmi')->store('sk-poktan', 'public'),
            'ttd_kembali_at' => now(),
        ]);

        return back()->with('success', 'Scan SK ' . $poktan->nomor_sk . ' terkirim ke admin.');
    }

    // ---- Rekap ----

    public function rekap(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $jenis = $request->input('jenis', 'solar');
        abort_unless(in_array($jenis, ['solar', 'pupuk'], true), 404);

        $model = $jenis === 'solar' ? SuratSolar::class : SuratPupuk::class;

        $rows = $model::selectRaw('MONTH(created_at) as bulan, status, COUNT(*) as jumlah')
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan', 'status')
            ->get();

        $rekap = [];
        foreach (range(1, 12) as $b) {
            $rekap[$b] = ['diajukan' => 0, 'diproses' => 0, 'diterima' => 0, 'ditolak' => 0, 'liter' => 0];
        }
        foreach ($rows as $r) {
            $rekap[$r->bulan][$r->status] = (int) $r->jumlah;
        }

        if ($jenis === 'solar') {
            $liter = SuratSolar::selectRaw('MONTH(created_at) as bulan, SUM(usulan_volume_konsumsi) as liter')
                ->whereYear('created_at', $tahun)->where('status', 'diterima')
                ->groupBy('bulan')->pluck('liter', 'bulan');
            foreach ($liter as $b => $l) {
                $rekap[$b]['liter'] = (float) $l;
            }
        }

        $ringkasan = [
            'total' => array_sum(array_map(fn ($r) => $r['diajukan'] + $r['diproses'] + $r['diterima'] + $r['ditolak'], $rekap)),
            'diterima' => array_sum(array_column($rekap, 'diterima')),
            'ditolak' => array_sum(array_column($rekap, 'ditolak')),
            'menunggu' => array_sum(array_column($rekap, 'diajukan')) + array_sum(array_column($rekap, 'diproses')),
        ];

        return view('ketua.rekap', compact('rekap', 'tahun', 'jenis', 'ringkasan'));
    }
}