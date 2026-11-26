<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\PoktanAnggota;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $id = $user->id;

        // Ambil seluruh pengajuan Solar & Pupuk milik user
        $solars = SuratSolar::where('user_id', $id)->latest()->get();
        $pupuks = SuratPupuk::with('poktan')->where('user_id', $id)->latest()->get();

        // Hitung distribusi status untuk Donut Chart & KPI
        $semuaStatus = $solars->pluck('status')->merge($pupuks->pluck('status'));

        $jumlahDiterima = $semuaStatus->filter(fn ($s) => in_array($s, ['diterima', 'disetujui'], true))->count();
        $jumlahProses = $semuaStatus->filter(fn ($s) => in_array($s, ['diajukan', 'diproses', 'pending'], true))->count();
        $jumlahDitolak = $semuaStatus->filter(fn ($s) => $s === 'ditolak')->count();
        $totalPengajuan = $semuaStatus->count();

        // Tren pengajuan 6 bulan terakhir (Bar Chart)
        $labelBulan = [];
        $dataSolarBulanan = [];
        $dataPupukBulanan = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $labelBulan[] = $bulan->translatedFormat('M Y');

            $dataSolarBulanan[] = $solars->filter(
                fn ($item) => $item->created_at && $item->created_at->year === $bulan->year && $item->created_at->month === $bulan->month
            )->count();

            $dataPupukBulanan[] = $pupuks->filter(
                fn ($item) => $item->created_at && $item->created_at->year === $bulan->year && $item->created_at->month === $bulan->month
            )->count();
        }

        // Gabungkan 5 pengajuan terbaru untuk Tabel Ringkasan
        $riwayatTerbaru = collect()
            ->merge($solars->map(fn ($s) => [
                'jenis' => 'BBM Solar Subsidi',
                'ikon' => 'bi-fuel-pump',
                'keterangan' => ($s->komoditas ?: 'Alat Mesin Pertanian') . ' · ' . ($s->luas_lahan_ha ? number_format((float) $s->luas_lahan_ha, 2, ',', '.') . ' ha' : '—'),
                'nomor' => $s->nomor_surat ?: 'Menunggu nomor',
                'tanggal' => $s->created_at,
                'status' => $s->status,
                'url_detail' => route('user.surat-solar.show', $s),
                'url_resmi' => ($s->status === 'diterima' && $s->diserahkan_at && $s->file_surat_resmi)
                    ? route('user.surat-solar.resmi', $s)
                    : null,
            ]))
            ->merge($pupuks->map(fn ($p) => [
                'jenis' => 'Pupuk Bersubsidi (RDKK)',
                'ikon' => 'bi-flower1',
                'keterangan' => ($p->poktan?->nama_kelompok ?? 'Kelompok Tani') . ' · Tahun ' . ($p->tahun_alokasi ?? now()->year),
                'nomor' => $p->nomor_surat ?: 'Menunggu nomor',
                'tanggal' => $p->created_at,
                'status' => $p->status,
                'url_detail' => route('user.surat-pupuk.show', $p),
                'url_resmi' => ($p->status === 'diterima' && $p->diserahkan_at && $p->file_surat_resmi)
                    ? route('user.surat-pupuk.resmi', $p)
                    : null,
            ]))
            ->sortByDesc('tanggal')
            ->take(5)
            ->values();

        // Info Kelembagaan Poktan aktif petani
        $anggotaPoktan = PoktanAnggota::with('poktan.desa')
            ->where(function ($q) use ($id, $user) {
                $q->where('user_id', $id);
                if ($user->nik) {
                    $q->orWhere('nik', $user->nik);
                }
            })
            ->where('status', '!=', 'nonaktif')
            ->latest()
            ->first();

        $poktanAktif = $anggotaPoktan?->poktan
            ?? ($user->poktan_id ? Poktan::with('desa')->find($user->poktan_id) : null);

        return view('user.dashboard', [
            'totalPengajuan' => $totalPengajuan,
            'jumlahDiterima' => $jumlahDiterima,
            'jumlahProses' => $jumlahProses,
            'jumlahDitolak' => $jumlahDitolak,
            'jumlahSolar' => $solars->count(),
            'jumlahPupuk' => $pupuks->count(),
            'labelBulan' => $labelBulan,
            'dataSolarBulanan' => $dataSolarBulanan,
            'dataPupukBulanan' => $dataPupukBulanan,
            'riwayatTerbaru' => $riwayatTerbaru,
            'poktanAktif' => $poktanAktif,
            'jabatanPoktan' => $anggotaPoktan?->jabatan ?? 'anggota',
        ]);
    }
}