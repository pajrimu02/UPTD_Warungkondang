<?php
// app/Http/Controllers/Ketua/DashboardController.php
namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $menunggu = fn ($m) => $m::whereNotNull('ttd_dikirim_at')->whereNull('ttd_kembali_at')->count();
        $bulanIni = fn ($m, $kolom) => $m::whereYear($kolom, now()->year)->whereMonth($kolom, now()->month)->count();

        return view('ketua.dashboard', [
            'menungguTtd' => $menunggu(\App\Models\SuratSolar::class) + $menunggu(\App\Models\SuratPupuk::class),
            'sudahTtdBulanIni' => $bulanIni(\App\Models\SuratSolar::class, 'ttd_kembali_at') + $bulanIni(\App\Models\SuratPupuk::class, 'ttd_kembali_at'),
            'diterimaBulanIni' => $bulanIni(\App\Models\SuratSolar::class, 'diserahkan_at') + $bulanIni(\App\Models\SuratPupuk::class, 'diserahkan_at'),
        ]);
    }
}