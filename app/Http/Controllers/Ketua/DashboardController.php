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
        return view('ketua.dashboard', [
            'menungguTtd' => SuratSolar::whereNotNull('ttd_dikirim_at')->whereNull('ttd_kembali_at')->count(),
            'sudahTtdBulanIni' => SuratSolar::whereYear('ttd_kembali_at', now()->year)
                ->whereMonth('ttd_kembali_at', now()->month)->count(),
            'diterimaBulanIni' => SuratSolar::where('status', 'diterima')
                ->whereYear('disetujui_at', now()->year)->whereMonth('disetujui_at', now()->month)->count(),
        ]);
    }
}