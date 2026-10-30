<?php
// app/Http/Controllers/Admin/SuratPengajuanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use Illuminate\View\View;

class SuratPengajuanController extends Controller
{
    public function index(): View
    {
        $menunggu = ['diajukan', 'diproses'];

        return view('admin.surat-pengajuan.index', [
            'cards' => [
                [
                    'judul' => 'BBM (Solar)',
                    'ikon' => 'bi-fuel-pump',
                    'deskripsi' => 'Pengajuan surat rekomendasi solar bersubsidi dari petani.',
                    'url' => route('admin.surat-solar.index'),
                    'total' => SuratSolar::count(),
                    'menunggu' => SuratSolar::whereIn('status', $menunggu)->count(),
                    'terbaru' => SuratSolar::latest()->value('created_at'),
                ],
                [
                    'judul' => 'Pupuk',
                    'ikon' => 'bi-flower2',
                    'deskripsi' => 'Pengajuan surat rekomendasi pupuk bersubsidi (RDKK) dari poktan.',
                    'url' => route('admin.surat-pupuk.index'),
                    'total' => SuratPupuk::count(),
                    'menunggu' => SuratPupuk::whereIn('status', $menunggu)->count(),
                    'terbaru' => SuratPupuk::latest()->value('created_at'),
                ],
            ],
        ]);
    }
}