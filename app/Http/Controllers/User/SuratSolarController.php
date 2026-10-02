<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuratSolarController extends Controller
{
    public function index(): View
    {
        $suratSolars = SuratSolar::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.surat-solar.index', compact('suratSolars'));
    }

    public function create(): View
    {
        return view('user.surat-solar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',

            'status_konsumen' => 'required|in:usaha_pertanian,usaha_perikanan,transportasi_motor_tempel,pekerjaan_umum',
            'jenis_usaha' => 'required|string|max:255',
            'nama_kapal' => 'nullable|required_if:status_konsumen,usaha_perikanan|string|max:255',

            'jenis_alat_mesin' => 'required|string|max:255',
            'fungsi_alat_mesin' => 'required|string|max:255',
            'jumlah_alat_mesin' => 'required|integer|min:1',
            'daya_alat_mesin' => 'nullable|string|max:100',
            'lama_penggunaan' => 'nullable|string|max:100',
            'lama_operasi' => 'nullable|string|max:100',
            'usulan_volume_konsumsi' => 'required|numeric|min:0',
            'volume_periode' => 'required|in:minggu,bulan,tiga_bulan',
            'estimasi_sisa_liter' => 'nullable|numeric|min:0',

            'file_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_sku' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_foto_mesin' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload berkas SEKALI SAJA (sebelumnya ada duplikasi di sini, sudah dibersihkan)
        foreach (['file_ktp', 'file_sku', 'file_foto_mesin'] as $field) {
            $validated[$field] = $request->file($field)->store('pengajuan-solar', 'public');
        }

        $spk = SuratSolar::hitungSkorKelayakan($request->user()->id, [
            $validated['file_ktp'],
            $validated['file_sku'],
            $validated['file_foto_mesin'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['spk_skor'] = $spk['skor'];
        $validated['spk_label'] = $spk['label'];
        $validated['status'] = $spk['label'] === 'layak' ? 'diproses' : 'diajukan';

        SuratSolar::create($validated);

        return redirect()->route('user.surat-solar.index')->with('status', 'Pengajuan berhasil dikirim.');
    }

    public function show(SuratSolar $surat_solar): View
    {
        abort_unless($surat_solar->user_id === auth()->id(), 403);

        return view('user.surat-solar.show', ['data' => $surat_solar]);
    }

    /**
     * Surat Resmi — status, nomor surat, PDF terbitan admin, catatan admin.
     */
    public function resmi(SuratSolar $surat_solar): View
    {
        abort_unless($surat_solar->user_id === auth()->id(), 403);

        return view('user.surat-solar.resmi', ['data' => $surat_solar]);
    }

    public function quickStore(Request $request): RedirectResponse
    {
        $terakhir = SuratSolar::where('user_id', $request->user()->id)->latest()->first();

        if (! $terakhir) {
            return redirect()->route('user.surat-solar.create')
                ->with('status', 'Belum ada data pengajuan sebelumnya, silakan isi form lengkap dulu.');
        }

        $baru = $terakhir->replicate([
            'status', 'nomor_surat', 'file_surat_resmi',
            'catatan_admin', 'spk_skor', 'spk_label',
            'disetujui_oleh', 'disetujui_at',
        ]);

        $spk = SuratSolar::hitungSkorKelayakan($request->user()->id, [
            $terakhir->file_ktp,
            $terakhir->file_sku,
            $terakhir->file_foto_mesin,
        ]);

        $baru->status = $spk['label'] === 'layak' ? 'diproses' : 'diajukan';
        $baru->spk_skor = $spk['skor'];
        $baru->spk_label = $spk['label'];
        $baru->nomor_surat = null;
        $baru->file_surat_resmi = null;
        $baru->catatan_admin = null;
        $baru->disetujui_oleh = null;
        $baru->disetujui_at = null;
        $baru->save();

        return redirect()->route('user.surat-solar.index')
            ->with('status', 'Pengajuan musim ini berhasil dikirim menggunakan data permohonan sebelumnya.');
    }
}