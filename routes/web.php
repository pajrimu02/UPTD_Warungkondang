<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PelayananController;
use App\Http\Controllers\Public\StokKuotaController;
use App\Http\Controllers\Public\InformasiController;
use App\Http\Controllers\Public\EdukasiController;
use App\Http\Controllers\Public\GaleriController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\KritikSaranController;

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SuratSolarController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\KritikSaranUserController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SuratSolarController as AdminSuratSolarController;
use App\Http\Controllers\Admin\KritikSaranController as AdminKritikSaranController;
use App\Http\Controllers\Admin\StokKuotaController as AdminStokKuotaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\PetaniController as AdminPetaniController;

// PUBLIK

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('tentang');

Route::prefix('informasi-publik')->name('infopublik.')->group(function () {
    Route::get('/pelayanan', [PelayananController::class, 'index'])->name('pelayanan');
    Route::get('/stok-kuota', [StokKuotaController::class, 'index'])->name('stokkuota');
    Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi');
    Route::get('/edukasi/{slug}', [EdukasiController::class, 'show'])->name('edukasi.show');
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::prefix('galeri')->name('galeri.')->group(function () {
    Route::get('/foto', [GaleriController::class, 'foto'])->name('foto');
    Route::get('/video', [GaleriController::class, 'video'])->name('video');
});

Route::get('/kritik-saran', [KritikSaranController::class, 'index'])->name('kritiksaran.publik');
Route::get('/hubungi-kami', [HomeController::class, 'hubungiKami'])->name('hubungi');

// USER

Route::middleware(['auth', 'role:user'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('surat-solar', SuratSolarController::class)
        ->only(['index', 'create', 'store', 'show']);
        Route::get('/surat-solar/{surat_solar}/resmi', [SuratSolarController::class, 'resmi'])->name('surat-solar.resmi');  
    Route::post('/surat-solar-cepat', [SuratSolarController::class, 'quickStore'])->name('surat-solar.quick-store');

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/kritik-saran', [KritikSaranUserController::class, 'create'])->name('kritiksaran.create');
    Route::post('/kritik-saran', [KritikSaranUserController::class, 'store'])->name('kritiksaran.store');
});

// ADMIN

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/surat-solar', [AdminSuratSolarController::class, 'index'])->name('surat-solar.index');
    Route::get('/surat-solar/{suratSolar}', [AdminSuratSolarController::class, 'show'])->name('surat-solar.show');
    Route::put('/surat-solar/{suratSolar}', [AdminSuratSolarController::class, 'update'])->name('surat-solar.update');
    Route::get('/surat-solar/{suratSolar}/lembar-kerja', [AdminSuratSolarController::class, 'lembarKerja'])->name('surat-solar.lembar-kerja');
Route::post('/surat-solar/{suratSolar}/upload-surat', [AdminSuratSolarController::class, 'uploadSurat'])->name('surat-solar.upload-surat');

    Route::get('/kritik-saran', [AdminKritikSaranController::class, 'index'])->name('kritiksaran.index');
    Route::get('/kritik-saran/{kritikSaran}', [AdminKritikSaranController::class, 'show'])->name('kritiksaran.show');
    Route::put('/kritik-saran/{kritikSaran}', [AdminKritikSaranController::class, 'update'])->name('kritiksaran.update');

    Route::resource('stok-kuota', AdminStokKuotaController::class)->except(['show']);
    Route::resource('galeri', AdminGaleriController::class)->except(['show']);

    Route::get('/petani', [AdminPetaniController::class, 'index'])->name('petani.index');
    Route::get('/petani/{user}/edit', [AdminPetaniController::class, 'edit'])->name('petani.edit');
    Route::put('/petani/{user}', [AdminPetaniController::class, 'update'])->name('petani.update');
    Route::delete('/petani/{user}', [AdminPetaniController::class, 'destroy'])->name('petani.destroy');
    Route::post('/petani/{user}/reset-password', [AdminPetaniController::class, 'resetPassword'])->name('petani.reset-password');
});

require __DIR__.'/auth.php';