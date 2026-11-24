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
        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif');

        return view('admin.poktan.show', [
            'poktan' => $poktan,
            'jumlah' => $aktif->count(),
            'totalLuas' => (float) $aktif->sum('luas_lahan_ha'),
            'minLuas' => (float) config('uptd.min_luas_poktan_ha', 30),
            'maksAnggota' => (int) config('uptd.maks_anggota_poktan', 35),
        ]);
    }

    public function kelola(Poktan $poktan): View
    {
        $this->muat($poktan);
        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif');
        $desa = $poktan->desa;

        return view('admin.poktan.kelola', [
            'poktan' => $poktan,
            'syarat' => $this->syarat($poktan),
            'jumlah' => $aktif->count(),
            'totalLuas' => (float) $aktif->sum('luas_lahan_ha'),
            'minLuas' => (float) config('uptd.min_luas_poktan_ha', 30),
            'maksAnggota' => (int) config('uptd.maks_anggota_poktan', 35),
            'sisaDesa' => ($desa && $desa->batasHa() !== null) ? $desa->sisaHa() : null,
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

    /* Jalur dibantu petugas */
    public function tambahAnggota(Request $request, Poktan $poktan): RedirectResponse
    {
        abort_unless(in_array($poktan->status, ['draft', 'diajukan'], true), 403);

        $data = $request->validate([
            'anggota' => ['required', 'array', 'min:1'],
            'anggota.*.nama' => ['required', 'string', 'max:255'],
            'anggota.*.nik' => ['required', 'digits:16', 'distinct'],
            'anggota.*.nomor_kk' => ['required', 'digits:16'],
            'anggota.*.tempat_lahir' => ['required', 'string', 'max:255'],
            'anggota.*.tanggal_lahir' => ['required', 'date', 'before:today'],
            'anggota.*.nama_ibu_kandung' => ['required', 'string', 'max:255'],
            'anggota.*.no_hp' => ['required', 'string', 'max:20'],
            'anggota.*.alamat' => ['required', 'string', 'max:500'],
            'anggota.*.luas_lahan_ha' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'anggota.*.jabatan' => ['required', 'in:ketua,sekretaris,bendahara,anggota'],
            'anggota.*.file_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'anggota.*.file_kk' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'anggota.*.file_sppt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'anggota.*.nik.digits' => 'Semua NIK harus 16 digit angka.',
            'anggota.*.nik.distinct' => 'Terdapat NIK yang sama di dalam form input.',
            'anggota.*.nomor_kk.digits' => 'Semua Nomor KK harus 16 digit angka.',
        ]);

        $daftarInput = $data['anggota'];
        $jumlahAktif = $poktan->anggota()->where('status', '!=', 'nonaktif')->count();
        $maksAnggota = (int) config('uptd.maks_anggota_poktan', 35);

        if (($jumlahAktif + count($daftarInput)) > $maksAnggota) {
            return back()->withErrors([
                'anggota' => 'Total anggota akan melebihi batas maksimal (' . $maksAnggota . ' orang). Sisa kuota: ' . max(0, $maksAnggota - $jumlahAktif) . ' orang.',
            ])->withInput();
        }

        // Cek bentrok NIK & Jabatan Ketua
        $nikInput = collect($daftarInput)->pluck('nik')->all();

        if ($poktan->anggota()->whereIn('nik', $nikInput)->where('status', '!=', 'nonaktif')->exists()) {
            return back()->withErrors(['anggota' => 'Salah satu NIK sudah terdaftar di poktan ini.'])->withInput();
        }

        $dipakaiLain = PoktanAnggota::whereIn('nik', $nikInput)
            ->where('poktan_id', '!=', $poktan->id)
            ->where('status', '!=', 'nonaktif')
            ->whereHas('poktan', fn ($q) => $q->whereIn('status', ['draft', 'diajukan', 'disetujui']))
            ->exists();

        if ($dipakaiLain) {
            return back()->withErrors(['anggota' => 'Salah satu NIK sudah terdaftar di poktan lain.'])->withInput();
        }

        $ketuaBaru = collect($daftarInput)->where('jabatan', 'ketua')->count();
        $ketuaAda = $poktan->anggota()->where('jabatan', 'ketua')->where('status', '!=', 'nonaktif')->exists();

        if (($ketuaAda && $ketuaBaru > 0) || $ketuaBaru > 1) {
            return back()->withErrors(['anggota' => 'Jabatan Ketua hanya boleh 1 orang dalam satu poktan.'])->withInput();
        }

        DB::transaction(function () use ($request, $poktan, $daftarInput) {
            foreach ($daftarInput as $idx => $item) {
                $paths = [];
                foreach (['file_ktp', 'file_kk', 'file_sppt'] as $field) {
                    $paths[$field] = $request->file("anggota.{$idx}.{$field}")->store('poktan/anggota', 'local');
                }

                $poktan->anggota()->create([
                    'nama' => $item['nama'],
                    'nik' => $item['nik'],
                    'nomor_kk' => $item['nomor_kk'],
                    'tempat_lahir' => $item['tempat_lahir'],
                    'tanggal_lahir' => $item['tanggal_lahir'],
                    'nama_ibu_kandung' => $item['nama_ibu_kandung'],
                    'no_hp' => $item['no_hp'],
                    'alamat' => $item['alamat'],
                    'luas_lahan_ha' => $item['luas_lahan_ha'],
                    'jabatan' => $item['jabatan'],
                    'file_ktp' => $paths['file_ktp'],
                    'file_kk' => $paths['file_kk'],
                    'file_sppt' => $paths['file_sppt'],
                    'status' => 'lengkap',
                    'dibantu_petugas' => true,
                    'user_id' => User::where('role', 'user')->where('nik', $item['nik'])->value('id'),
                ]);
            }

            $this->hitungUlang($poktan);
        });

        return redirect()->to(route('admin.poktan.kelola', $poktan) . '#anggota')
            ->with('success', count($daftarInput) . ' anggota berhasil ditambahkan sekaligus (dibantu petugas).');
    }

    /** Edit data & berkas anggota yang sudah terdaftar sebelum poktan disetujui */
    public function updateAnggota(Request $request, Poktan $poktan, PoktanAnggota$anggota): RedirectResponse
    {
        abort_unless(
            in_array($poktan->status, ['draft', 'diajukan'], true) && (int) $anggota->poktan_id === (int)$poktan->id,
            403
        );

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
            'file_ktp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_kk' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_sppt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'nik.digits' => 'NIK harus 16 digit angka.',
            'nomor_kk.digits' => 'Nomor KK harus 16 digit angka.',
        ]);

        // Cek duplikat NIK di poktan yang sama (kecuali dirinya sendiri)
        if ($poktan->anggota()->where('nik', $data['nik'])->where('id', '!=',$anggota->id)->where('status', '!=', 'nonaktif')->exists()) {
            return back()->with('error', 'NIK ' . $data['nik'] . ' sudah dipakai anggota lain di poktan ini.');
        }

        // Cek duplikat NIK di poktan lain
        $dipakaiLain = PoktanAnggota::where('nik',$data['nik'])
            ->where('poktan_id', '!=', $poktan->id)
            ->where('status', '!=', 'nonaktif')
            ->whereHas('poktan', fn ($q) =>$q->whereIn('status', ['draft', 'diajukan', 'disetujui']))
            ->exists();

        if ($dipakaiLain) {
            return back()->with('error', 'NIK ' . $data['nik'] . ' sudah terdaftar di poktan lain.');
        }

        // Ketua tidak bisa diturunkan jika tidak ada ketua lain, dan jabatan ketua tidak boleh ganda
        if ($anggota->jabatan === 'ketua' &&$data['jabatan'] !== 'ketua') {
            return back()->with('error', 'Jabatan Ketua tidak bisa diubah langsung. Ubah anggota lain menjadi Ketua terlebih dahulu.');
        }

        if ($data['jabatan'] === 'ketua' && $anggota->jabatan !== 'ketua') {$ketuaLama = $poktan->anggota()->where('jabatan', 'ketua')->where('id', '!=',$anggota->id)->first();
            if ($ketuaLama) {$ketuaLama->update(['jabatan' => 'anggota']);
            }
        }

        // Ganti file hanya jika diunggah ulang
        foreach (['file_ktp', 'file_kk', 'file_sppt'] as $field) {
            if ($request->hasFile($field)) {
                if ($anggota->{$field}) {
                    Storage::disk('local')->delete($anggota->{$field});
                }
                $data[$field] = $request->file($field)->store('poktan/anggota', 'local');
            } else {
                unset($data[$field]);
            }
        }

        $anggota->fill($data);$anggota->status = ($anggota->file_ktp &&$anggota->file_kk && $anggota->file_sppt) ? 'lengkap' : 'menunggu';$anggota->save();

        $this->hitungUlang($poktan);

        return redirect()->to(route('admin.poktan.kelola', $poktan) . '#anggota')
            ->with('success', 'Data anggota "' . $anggota->nama . '" berhasil diperbarui.');
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

    public function destroy(Poktan $poktan): RedirectResponse
    {
        if (! in_array($poktan->status, ['draft', 'ditolak'], true)) {
            return back()->with('error', 'Poktan yang sedang diajukan atau sudah disetujui tidak dapat dihapus.');
        }

        if ($poktan->suratSolars()->exists() || $poktan->suratPupuks()->exists()) {
            return back()->with('error', 'Poktan memiliki riwayat pengajuan surat sehingga tidak dapat dihapus.');
        }

        DB::transaction(function () use ($poktan) {
            foreach ($poktan->anggota as $a) {
                Storage::disk('local')->delete(array_filter([$a->file_ktp, $a->file_kk, $a->file_sppt]));
            }
            $poktan->anggota()->delete();
            $poktan->delete();
        });

        return redirect()->route('admin.poktan.index')
            ->with('success', 'Data pengajuan poktan "' . $poktan->nama_kelompok . '" dihapus.');
    }
}