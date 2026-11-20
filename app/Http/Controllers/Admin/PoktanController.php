<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\PoktanAnggota;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PoktanController extends Controller
{
    public function index(Request $request): View
    {
        $poktans = Poktan::with('desa', 'pengaju')->withCount('anggota')
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status',$request->status),
                fn ($q) =>$q->where('status', '!=', 'draft')
            )
            ->orderByRaw("FIELD(status, 'diajukan', 'disetujui', 'ditolak', 'draft')")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.poktan.index', compact('poktans'));
    }

    public function show(Poktan $poktan): View
    {
        $this->muat($poktan);
        $aktif =$poktan->anggota->where('status', '!=', 'nonaktif');
        $desa =$poktan->desa;

        return view('admin.poktan.show', [
            'poktan' => $poktan,
            'syarat' => $this->syarat($poktan),
            'jumlah' => $aktif->count(),
            'totalLuas' => (float) $aktif->sum('luas_lahan_ha'),
            'minLuas' => (float) config('uptd.min_luas_poktan_ha', 30),
            'maksAnggota' => (int) config('uptd.maks_anggota_poktan', 35),
            'sisaDesa' => ($desa && $desa->batasHa() !== null) ?$desa->sisaHa() : null,
            'bolehUbah' => in_array($poktan->status, ['draft', 'diajukan'], true),
        ]);
    }

    public function update(Request $request, Poktan$poktan): RedirectResponse
    {
        abort_unless($poktan->status === 'diajukan', 403);

        $data =$request->validate([
            'keputusan' => ['required', 'in:disetujui,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['keputusan'] === 'ditolak') {
            if (blank($data['catatan_admin'] ?? null)) {
                return back()->withErrors(['catatan_admin' => 'Isi alasan penolakan.'])->withInput();
            }

            $poktan->update([
                'status' => 'ditolak',
                'catatan_admin' => $data['catatan_admin'],
                'disetujui_at' => null,
            ]);

            return redirect()->route('admin.poktan.show', $poktan)
                ->with('success', 'Poktan "' . $poktan->nama_kelompok . '" ditolak.');
        }

        // Disetujui: semua syarat harus terpenuhi
        $this->muat($poktan);$gagal = collect($this->syarat($poktan))->where('ok', false);

        if ($gagal->isNotEmpty()) {
            return back()->with('error', 'Belum bisa disetujui: ' . $gagal->pluck('label')->implode('; ') . '.');
        }

        $aktif =$poktan->anggota->where('status', '!=', 'nonaktif');

        DB::transaction(function () use ($poktan, $aktif,$data) {
            $this->hitungUlang($poktan);

            $poktan->update([
                'status' => 'disetujui',
                'nomor_sk' => $poktan->nomor_sk ?? Poktan::generateNomorSK(),
                'disetujui_oleh' => auth()->id(),
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'disetujui_at' => now(),
                'dikunci_at' => $poktan->dikunci_at ?? now(),
            ]);

            // Hubungkan akun yang sudah punya anggota (user_id) atau yang NIK-nya sama
            $nik =$aktif->pluck('nik')->filter()->all();
            $akunPerNik = User::where('role', 'user')->whereIn('nik',$nik)->pluck('id', 'nik');

            foreach ($aktif->whereNull('user_id') as$a) {
                if ($uid = $akunPerNik[$a->nik] ?? null) {
                    $a->update(['user_id' =>$uid]);
                }
            }

            $idAkun =$aktif->pluck('user_id')->filter()
                ->merge($akunPerNik->values())
                ->push($poktan->diajukan_oleh)
                ->filter()->unique()->all();

            User::whereIn('id', $idAkun)->update(['poktan_id' =>$poktan->id]);
        });

        return redirect()->route('admin.poktan.show', $poktan)
            ->with('success', 'Poktan "' . $poktan->nama_kelompok . '" disetujui dengan Nomor SK ' . $poktan->fresh()->nomor_sk . '.');
    }

    /** Tahapan TTD SK Pengukuhan ke Kepala UPTD & Penerbitan ke Petani */
    public function tahap(Request $request, Poktan$poktan): RedirectResponse
    {
        abort_unless($poktan->status === 'disetujui', 403);

        $aksi =$request->validate([
            'aksi' => ['required', 'in:kirim_kepala,kirim_petani'],
        ])['aksi'];

        if ($aksi === 'kirim_kepala') {
            abort_if($poktan->ttd_dikirim_at, 403);

            $poktan->update(['ttd_dikirim_at' => now()]);

            return back()->with('success', 'SK Pengukuhan dikirim ke Kepala UPTD untuk ditandatangani.');
        }

        abort_unless($poktan->ttd_kembali_at && !$poktan->diserahkan_at, 403);

        $poktan->update(['diserahkan_at' => now()]);

        return back()->with('success', 'SK Pengukuhan resmi telah dikirim ke Ketua Poktan/Petani.');
    }

    /** Jalur dibantu petugas: admin mengisi data dan berkas untuk petani yang tidak bisa mendaftar sendiri. */
    public function tambahAnggota(Request $request, Poktan$poktan): RedirectResponse
    {
        abort_unless(in_array($poktan->status, ['draft', 'diajukan'], true), 403);

        $data =$request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'nomor_kk' => ['required', 'digits:16'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'nama_ibu_kandung' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'luas_lahan_ha' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'jabatan' => ['required', 'in:ketua,sekretaris,bendahara,anggota'],
            'file_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_kk' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_sppt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'nik.digits' => 'NIK harus 16 digit angka.',
            'nomor_kk.digits' => 'Nomor KK harus 16 digit angka.',
        ]);

        $aktif =$poktan->anggota()->where('status', '!=', 'nonaktif');

        if ($aktif->count() >= (int) config('uptd.maks_anggota_poktan', 35)) {
            return back()->withErrors(['nama' => 'Jumlah anggota sudah mencapai batas maksimal.'])->withInput();
        }

        if ($poktan->anggota()->where('nik',$data['nik'])->where('status', '!=', 'nonaktif')->exists()) {
            return back()->withErrors(['nik' => 'NIK ini sudah ada di poktan ini.'])->withInput();
        }

        $dipakaiLain = PoktanAnggota::where('nik',$data['nik'])
            ->where('poktan_id', '!=', $poktan->id)
            ->where('status', '!=', 'nonaktif')
            ->whereHas('poktan', fn ($q) =>$q->whereIn('status', ['draft', 'diajukan', 'disetujui']))
            ->exists();
        if ($dipakaiLain) {
            return back()->withErrors(['nik' => 'NIK ini sudah terdaftar di poktan lain.'])->withInput();
        }

        if ($data['jabatan'] === 'ketua' &&$poktan->anggota()->where('jabatan', 'ketua')->where('status', '!=', 'nonaktif')->exists()) {
            return back()->withErrors(['jabatan' => 'Ketua sudah ada. Pilih jabatan lain.'])->withInput();
        }

        foreach (['file_ktp', 'file_kk', 'file_sppt'] as $field) {
            $data[$field] = $request->file($field)->store('poktan/anggota', 'local');
        }

        $poktan->anggota()->create($data + [
            'status' => 'lengkap',
            'dibantu_petugas' => true,
            'user_id' => User::where('role', 'user')->where('nik', $data['nik'])->value('id'),
        ]);

        $this->hitungUlang($poktan);

        return redirect()->to(route('admin.poktan.show', $poktan) . '#anggota')
            ->with('success', 'Anggota "' . $data['nama'] . '" ditambahkan (dibantu petugas).');
    }

    public function hapusAnggota(Poktan $poktan, PoktanAnggota$anggota): RedirectResponse
    {
        abort_unless(
            in_array($poktan->status, ['draft', 'diajukan'], true) && (int) $anggota->poktan_id === (int)$poktan->id,
            403
        );

        if ($anggota->jabatan === 'ketua') {
            return back()->with('error', 'Ketua tidak dapat dihapus. Tolak pengajuan jika data ketua bermasalah.');
        }

        Storage::disk('local')->delete(array_filter([$anggota->file_ktp, $anggota->file_kk,$anggota->file_sppt]));
        $nama =$anggota->nama;
        $anggota->delete();

        $this->hitungUlang($poktan);

        return redirect()->to(route('admin.poktan.show', $poktan) . '#anggota')
            ->with('success', 'Anggota "' . $nama . '" dihapus.');
    }

    public function berkas(Poktan $poktan, PoktanAnggota $anggota, string$jenis)
    {
        abort_unless(in_array($jenis, ['ktp', 'kk', 'sppt'], true) && (int) $anggota->poktan_id === (int)$poktan->id, 404);

        $path = $anggota->{'file_' .$jenis};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    public function skPreview(Poktan $poktan)
    {
        $this->muat($poktan);

        return Pdf::loadView('pdf.sk-pengukuhan-poktan', [
            'poktan' => $poktan,
            'draf' => $poktan->status !== 'disetujui',
        ])
            ->setPaper([0, 0, 609.45, 935.43])
            ->stream('SK-Poktan-' . $poktan->id . '.pdf');
    }

    // ------------------------------------------------------------------

    private function muat(Poktan $poktan): void
    {
        $poktan->load([
            'desa', 'pengaju',
            'anggota' => fn ($q) =>$q
                ->orderByRaw("FIELD(jabatan, 'ketua', 'sekretaris', 'bendahara', 'anggota')")
                ->orderBy('nama'),
        ]);
    }

    private function hitungUlang(Poktan $poktan): void
    {
        $aktif =$poktan->anggota()->where('status', '!=', 'nonaktif')->get();

        $poktan->update([
            'jumlah_anggota' => $aktif->count(),
            'luas_lahan_ha' => $aktif->sum('luas_lahan_ha'),
            'nama_ketua' => $aktif->firstWhere('jabatan', 'ketua')?->nama ?? $poktan->nama_ketua,
        ]);
    }

    /** Daftar syarat persetujuan: tiap item berisi label, ok, ket. */
    private function syarat(Poktan $poktan): array
    {
        $aktif =$poktan->anggota->where('status', '!=', 'nonaktif');
        $jumlah =$aktif->count();
        $total = (float)$aktif->sum('luas_lahan_ha');
        $min = (float) config('uptd.min_luas_poktan_ha', 30);$maks = (int) config('uptd.maks_anggota_poktan', 35);
        $desa =$poktan->desa;
        $fmt = fn ($n) => number_format($n, 2, ',', '.');

        $nik =$aktif->pluck('nik')->filter()->all();
        $ganda =$nik
            ? PoktanAnggota::whereIn('nik', $nik)->where('poktan_id', '!=',$poktan->id)
                ->whereHas('poktan', fn ($q) =>$q->where('status', 'disetujui'))->count()
            : 0;

        $menunggu =$aktif->where('status', 'menunggu')->count();
        $tanpaBerkas =$aktif->filter(fn ($a) => !$a->file_ktp || ! $a->file_kk || !$a->file_sppt)->count();
        $jumlahKetua =$aktif->where('jabatan', 'ketua')->count();

        if (! $desa) {$desaOk = false;
            $desaKet = 'Desa belum dipilih';
        } elseif ($desa->batasHa() === null) {$desaOk = true;
            $desaKet = 'Tidak ada batas desa yang berlaku';
        } else {
            $desaOk = $total <=$desa->sisaHa();
            $desaKet =$fmt($total) . ' ha dari sisa ' .$fmt($desa->sisaHa()) . ' ha' . ($desa->memakaiBatasBawaan() ? ' (batas sementara)' : '');
        }

        return [
            ['label' => 'Tepat 1 ketua', 'ok' => $jumlahKetua === 1, 'ket' =>$jumlahKetua . ' ketua'],
            ['label' => 'Jumlah anggota maksimal ' . $maks, 'ok' => $jumlah >= 1 &&$jumlah <= $maks, 'ket' =>$jumlah . ' anggota'],
            ['label' => 'Semua anggota sudah melengkapi data', 'ok' => $menunggu === 0, 'ket' => $menunggu ? $menunggu . ' menunggu diisi' : 'Lengkap'],
            ['label' => 'Berkas KTP, KK, SPPT lengkap', 'ok' => $tanpaBerkas === 0, 'ket' => $tanpaBerkas ? $tanpaBerkas . ' anggota kurang berkas' : 'Lengkap'],
            ['label' => 'Total luas minimal ' . $fmt($min) . ' ha', 'ok' => $total >=$min, 'ket' => $fmt($total) . ' ha'],
            ['label' => 'Tidak melebihi batas luas desa', 'ok' => $desaOk, 'ket' =>$desaKet],
            ['label' => 'Tidak ada NIK di poktan lain yang disetujui', 'ok' => $ganda === 0, 'ket' => $ganda ? $ganda . ' NIK bentrok' : 'Aman'],
        ];
    }
}