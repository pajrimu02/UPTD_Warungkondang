<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Controller;
use App\Models\SuratSolar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuratSolarController extends Controller
{
    public function index(): View
    {
        $suratSolars = SuratSolar::where('user_id', auth()->id())->latest()->paginate(10);

        return view('user.surat-solar.index', compact('suratSolars'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $poktan = $user->poktanDisetujui(); // opsional, boleh null

        $anggota = ($poktan && $user->nik)
            ? $poktan->anggota()->where('nik', $user->nik)->first()
            : null;

        $isian = [
            'nama_pemohon' => $user->name,
            'nik' => $user->nik,
            'alamat' => $user->alamat ?: ($anggota?->alamat ?: $poktan?->alamat),
            'no_hp' => $user->no_hp ?: $anggota?->no_hp,
            'status_konsumen' => $user->konsumen_pengguna,
            'jenis_usaha' => $user->jenis_usaha,
            'nama_kapal' => $user->nama_kapal,
        ];

        return view('user.surat-solar.create', [
            'poktan' => $poktan,
            'isian' => $isian,
            'konsumen' => RegisteredUserController::KONSUMEN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $poktan = $request->user()->poktanDisetujui();  

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',

            'status_konsumen' => ['required', Rule::in(array_keys(RegisteredUserController::KONSUMEN))],
            'jenis_usaha' => 'required|string|max:255',
            'nama_kapal' => 'nullable|required_if:status_konsumen,usaha_perikanan|string|max:255',

            'jenis_alat_mesin' => 'required|string|max:255',
            'jenis_alat_mesin_lainnya' => 'nullable|required_if:jenis_alat_mesin,lainnya|string|max:255',
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

        if ($validated['jenis_alat_mesin'] === 'lainnya') {
            $validated['jenis_alat_mesin'] = $validated['jenis_alat_mesin_lainnya'];
        }
        unset($validated['jenis_alat_mesin_lainnya']);

        foreach (['file_ktp', 'file_sku', 'file_foto_mesin'] as $field) {
            $validated[$field] = $request->file($field)->store('pengajuan-solar', 'public');
        }

        $spk = SuratSolar::hitungSkorKelayakan($request->user()->id, [
            $validated['file_ktp'], $validated['file_sku'], $validated['file_foto_mesin'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['poktan_id'] = $poktan?->id;  
        $validated['spk_skor'] = $spk['skor'];
        $validated['spk_label'] = $spk['label'];
        $validated['status'] = $spk['label'] === 'diajukan';

        SuratSolar::create($validated);

        return redirect()->route('user.surat-solar.index')->with('status', 'Pengajuan berhasil dikirim.');
    }

    public function show(SuratSolar $surat_solar): View
    {
        abort_unless($surat_solar->user_id === auth()->id(), 403);

        return view('user.surat-solar.show', ['data' => $surat_solar]);
    }

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
            'status', 'nomor_surat', 'file_surat_resmi', 'catatan_admin',
            'spk_skor', 'spk_label', 'disetujui_oleh', 'disetujui_at',
            'ttd_dikirim_at', 'ttd_kembali_at', 'diserahkan_at',
            'alokasi_volume', 'tempat_pengambilan', 'nomor_penyalur',
            'alamat_penyalur', 'alat_pembelian', 'berlaku_sampai',
        ]);

        $spk = SuratSolar::hitungSkorKelayakan($request->user()->id, [
            $terakhir->file_ktp, $terakhir->file_sku, $terakhir->file_foto_mesin,
        ]);

        $baru->poktan_id = $request->user()->poktanDisetujui()?->id;
        $baru->status = $spk['label'] === 'diajukan';
        $baru->spk_skor = $spk['skor'];
        $baru->spk_label = $spk['label'];
        $baru->save();

        return redirect()->route('user.surat-solar.index')
            ->with('status', 'Pengajuan musim ini berhasil dikirim menggunakan data permohonan sebelumnya.');
    }
}