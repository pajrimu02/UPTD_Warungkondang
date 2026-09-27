<?php
 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use App\Models\KritikSaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUser = User::where('role', 'user')->count();

        $totalSuratSolar = SuratSolar::count();
        $suratPending = SuratSolar::where('status', 'pending')->count();
        $suratDiterima = SuratSolar::where('status', 'diterima')->count();
        $suratDitolak = SuratSolar::where('status', 'ditolak')->count();

        $totalKritikSaran = KritikSaran::count();
        $kritikPending = KritikSaran::where('status', 'pending')->count();

        $suratTerbaru = SuratSolar::with('user')->latest()->limit(5)->get();
        $kritikTerbaru = KritikSaran::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUser',
            'totalSuratSolar',
            'suratPending',
            'suratDiterima',
            'suratDitolak',
            'totalKritikSaran',
            'kritikPending',
            'suratTerbaru',
            'kritikTerbaru',
        ));
    }
}