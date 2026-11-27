<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KritikSaran;
use App\Models\Poktan;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $menunggu = ['diajukan', 'diproses'];

        return view('admin.dashboard', [
            'totalUser' => User::where('role', 'user')->count(),
            'totalPoktan' => Poktan::where('status', 'disetujui')->count(),
            'totalSuratSolar' => SuratSolar::count(),
            'totalSuratPupuk' => SuratPupuk::count(),
            'solarPending' => SuratSolar::whereIn('status', $menunggu)->count(),
            'pupukPending' => SuratPupuk::whereIn('status', $menunggu)->count(),
            'poktanPending' => Poktan::where('status', 'diajukan')->count(),
            'kritikPending' => KritikSaran::where('status', 'pending')->count(),

            'poktanTerbaru' => Poktan::with('desa')->withCount('anggota')->latest()->limit(5)->get(),
            'suratTerbaru' => SuratSolar::with('user')->latest()->limit(5)->get(),
            'pupukTerbaru' => SuratPupuk::with('poktan')->withCount('items')->latest()->limit(5)->get(),
            'kritikTerbaru' => KritikSaran::with('user')->latest()->limit(5)->get(),
        ]);
    }
}