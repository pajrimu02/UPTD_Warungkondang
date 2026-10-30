<?php
// app/Http/Controllers/Ketua/SuratController.php
namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\SuratPupuk;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratController extends Controller
{
    public function index(): View
    {
        $menunggu = SuratSolar::with('user', 'poktan')
            ->whereNotNull('ttd_dikirim_at')->whereNull('ttd_kembali_at')
            ->orderBy('ttd_dikirim_at')->get();

        $selesai = SuratSolar::with('user')
            ->whereNotNull('ttd_kembali_at')
            ->latest('ttd_kembali_at')->limit(10)->get();

        return view('ketua.surat.index', compact('menunggu', 'selesai'));
    }

    public function cetakSolar(SuratSolar $suratSolar)
{
    abort_unless($suratSolar->ttd_dikirim_at, 403);
    $suratSolar->load('user', 'poktan');

    return Pdf::loadView('pdf.surat-rekomendasi-solar', compact('suratSolar'))
        ->setPaper('a4')
        ->stream('Surat-Rekomendasi-' . $suratSolar->id . '.pdf');
    }

    public function uploadSolar(Request $request, SuratSolar $suratSolar): RedirectResponse
    {
        abort_unless($suratSolar->ttd_dikirim_at && ! $suratSolar->ttd_kembali_at, 403);

        $request->validate([
            'file_surat_resmi' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $suratSolar->update([
            'file_surat_resmi' => $request->file('file_surat_resmi')->store('surat-resmi', 'public'),
            'ttd_kembali_at' => now(),
        ]);

        return back()->with('success', 'Scan surat ' . $suratSolar->nomor_surat . ' terkirim ke admin.');
    }

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