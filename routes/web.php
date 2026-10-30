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
use App\Http\Controllers\User\PoktanController;
use App\Http\Controllers\User\SuratSolarController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\KritikSaranUserController;
use App\Http\Controllers\User\SuratPengajuanController;
use App\Http\Controllers\User\SuratPupukController;

use App\Http\Controllers\Admin\SuratPengajuanController as AdminSuratPengajuanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SuratSolarController as AdminSuratSolarController;
use App\Http\Controllers\Admin\KritikSaranController as AdminKritikSaranController;
use App\Http\Controllers\Admin\StokKuotaController as AdminStokKuotaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\PetaniController as AdminPetaniController;
use App\Http\Controllers\Admin\SuratPupukController as AdminSuratPupukController;
use App\Http\Controllers\Admin\PoktanController as AdminPoktanController;
use App\Http\Controllers\Admin\DesaController as AdminDesaController;

use App\Http\Controllers\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Ketua\SuratController as KetuaSuratController;

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

    Route::get('/poktan', [PoktanController::class, 'index'])->name('poktan');
    Route::get('/poktan/ajukan', [PoktanController::class, 'create'])->name('poktan.create');
    Route::post('/poktan', [PoktanController::class, 'store'])->name('poktan.store');
    Route::get('/poktan/{poktan}', [PoktanController::class, 'show'])->name('poktan.show');
    Route::get('/poktan/{poktan}/anggota', [PoktanController::class, 'anggota'])->name('poktan.anggota');
    Route::post('/poktan/{poktan}/anggota', [PoktanController::class, 'anggotaStore'])->name('poktan.anggota.store');
    Route::delete('/poktan/{poktan}/anggota/{anggota}', [PoktanController::class, 'anggotaDestroy'])->name('poktan.anggota.destroy');
    Route::get('/poktan/{poktan}/anggota/{anggota}/berkas/{jenis}', [PoktanController::class, 'berkas'])->name('poktan.berkas');
    Route::post('/poktan/{poktan}/selesai', [PoktanController::class, 'selesai'])->name('poktan.selesai');

    Route::get('/surat-pengajuan', [SuratPengajuanController::class, 'index'])->name('surat-pengajuan.index');
    Route::resource('surat-solar', SuratSolarController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/surat-solar/{surat_solar}/resmi', [SuratSolarController::class, 'resmi'])->name('surat-solar.resmi');
    Route::post('/surat-solar-cepat', [SuratSolarController::class, 'quickStore'])->name('surat-solar.quick-store');
    
    Route::resource('surat-pupuk', SuratPupukController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/surat-pupuk/{surat_pupuk}/resmi', [SuratPupukController::class, 'resmi'])->name('surat-pupuk.resmi');
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
    Route::get('/surat-solar/{suratSolar}/kelola', [AdminSuratSolarController::class, 'kelola'])->name('surat-solar.kelola');
    Route::post('/surat-solar/{suratSolar}/tahap', [AdminSuratSolarController::class, 'tahap'])->name('surat-solar.tahap');
    Route::delete('/surat-solar/{suratSolar}', [AdminSuratSolarController::class, 'destroy'])->name('surat-solar.destroy');

    Route::get('/surat-pupuk', [AdminSuratPupukController::class, 'index'])->name('surat-pupuk.index');
    Route::get('/surat-pupuk/{suratPupuk}', [AdminSuratPupukController::class, 'show'])->name('surat-pupuk.show');
    Route::put('/surat-pupuk/{suratPupuk}', [AdminSuratPupukController::class, 'update'])->name('surat-pupuk.update');
    Route::get('/surat-pupuk/{suratPupuk}/lembar-kerja', [AdminSuratPupukController::class, 'lembarKerja'])->name('surat-pupuk.lembar-kerja');
    Route::post('/surat-pupuk/{suratPupuk}/upload-surat', [AdminSuratPupukController::class, 'uploadSurat'])->name('surat-pupuk.upload-surat');
    Route::get('/surat-pengajuan', [AdminSuratPengajuanController::class, 'index'])->name('surat-pengajuan.index');
    Route::get('/poktan', [AdminPoktanController::class, 'index'])->name('poktan.index');
    Route::get('/poktan/{poktan}', [AdminPoktanController::class, 'show'])->name('poktan.show');
    Route::put('/poktan/{poktan}', [AdminPoktanController::class, 'update'])->name('poktan.update');
    Route::get('/poktan/{poktan}/anggota/{anggota}/berkas/{jenis}', [AdminPoktanController::class, 'berkas'])->name('poktan.berkas');

    Route::resource('desa', AdminDesaController::class)->only(['index', 'store', 'update', 'destroy']);

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

// KETUA
Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/', [KetuaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/surat', [KetuaSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/solar/{suratSolar}/cetak', [KetuaSuratController::class, 'cetakSolar'])->name('surat.cetak-solar');
    Route::post('/surat/solar/{suratSolar}/upload', [KetuaSuratController::class, 'uploadSolar'])->name('surat.upload-solar');
    Route::get('/rekap', [KetuaSuratController::class, 'rekap'])->name('rekap');
});

require __DIR__.'/auth.php';