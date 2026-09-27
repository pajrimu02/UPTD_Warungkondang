<?php

use Illuminate\Support\Facades\Route;

// ==========================================================
// 1) PUBLIK — tanpa login
// ==========================================================
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PelayananController;
use App\Http\Controllers\Public\StokKuotaController;
use App\Http\Controllers\Public\InformasiController;
use App\Http\Controllers\Public\EdukasiController;
use App\Http\Controllers\Public\GaleriController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\KritikSaranController; // versi publik: read-only

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('tentang');

// Dropdown "Informasi Publik"
Route::prefix('informasi-publik')->name('infopublik.')->group(function () {
    Route::get('/pelayanan', [PelayananController::class, 'index'])->name('pelayanan');
    Route::get('/stok-kuota', [StokKuotaController::class, 'index'])->name('stokkuota');
    Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi');
    Route::get('/edukasi/{slug}', [EdukasiController::class, 'show'])->name('edukasi.show');
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Dropdown "Galeri"
Route::prefix('galeri')->name('galeri.')->group(function () {
    Route::get('/foto', [GaleriController::class, 'foto'])->name('foto');
    Route::get('/video', [GaleriController::class, 'video'])->name('video');
});

// Publik: hanya baca daftar kritik & saran (bukan form submit)
Route::get('/kritik-saran', [KritikSaranController::class, 'index'])->name('kritiksaran.publik');

Route::get('/hubungi-kami', [HomeController::class, 'hubungiKami'])->name('hubungi');


// ==========================================================
// 2) USER — perlu login, role: user
// ==========================================================
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SuratSolarController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\KritikSaranUserController;  

Route::middleware(['auth', 'role:user'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('surat-solar', SuratSolarController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');      

    Route::get('/kritik-saran', [KritikSaranUserController::class, 'create'])->name('kritiksaran.create');
    Route::post('/kritik-saran', [KritikSaranUserController::class, 'store'])->name('kritiksaran.store');
});


// ==========================================================
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SuratSolarController as AdminSuratSolarController;
use App\Http\Controllers\Admin\KritikSaranController as AdminKritikSaranController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/surat-solar', [AdminSuratSolarController::class, 'index'])->name('surat-solar.index');
    Route::get('/surat-solar/{suratSolar}', [AdminSuratSolarController::class, 'show'])->name('surat-solar.show');
    Route::put('/surat-solar/{suratSolar}', [AdminSuratSolarController::class, 'update'])->name('surat-solar.update');

    Route::get('/kritik-saran', [AdminKritikSaranController::class, 'index'])->name('kritiksaran.index');
    Route::get('/kritik-saran/{kritikSaran}', [AdminKritikSaranController::class, 'show'])->name('kritiksaran.show');
    Route::put('/kritik-saran/{kritikSaran}', [AdminKritikSaranController::class, 'update'])->name('kritiksaran.update');
});
 


require __DIR__.'/auth.php'; // dari Laravel Breeze
