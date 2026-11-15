<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Poktan;
use App\Models\PoktanAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PoktanController extends Controller
{
    // Poktan yang masih "memegang" anggotanya
    private const STATUS_TERIKAT = ['draft', 'diajukan', 'disetujui', 'ditolak'];

    private function minLuas(): float
    {
        return (float) config('uptd.min_luas_poktan_ha', 30);
    }

    private function maksAnggota(): int
    {
        return (int) config('uptd.maks_anggota_poktan', 35);
    }

    // TODO: konfirmasi minimal anggota resmi ke UPTD (bisa diisi di config/uptd.php: 'min_anggota_poktan')
    private function minAnggota(): int
    {
        return (int) config('uptd.min_anggota_poktan', 1);
    }

    // ------------------------------------------------------------------ halaman utama

    public function index(Request $request): View
    {
        $user = $request->user();
        $poktan = $this->poktanSaya($user);
        $saya = null;
        $isKetua = false;

        if ($poktan) {
            $poktan->loadCount(['anggota as anggota_count' => fn ($q) => $q->where('status', '!=', 'nonaktif')]);
            $poktan->loadSum(['anggota as luas_total' => fn ($q) => $q->where('status', '!=', 'nonaktif')], 'luas_lahan_ha');
            $saya = $this->barisSaya($poktan);
            $isKetua = $this->adalahKetua($poktan);
        }

        return view('user.poktan.index', [
            'poktan' => $poktan,
            'saya' => $saya,
            'isKetua' => $isKetua,
            'minLuas' => $this->minLuas(),
            'maksAnggota' => $this->maksAnggota(),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($stop = $this->halangan($request)) {
            return $stop;
        }

        return view('user.poktan.create', ['desas' => Desa::orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($stop = $this->halangan($request)) {
            return $stop;
        }

        $user = $request->user();

        $data = $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:255',
                Rule::unique('poktans', 'nama_kelompok')->where(fn ($q) => $q->whereIn('status', ['diajukan', 'disetujui']))],
            'desa_id' => ['required', 'exists:desas,id'],
            'alamat' => ['required', 'string', 'max:500'],
        ]);

        $poktan = DB::transaction(function () use ($data, $user) {
            $poktan = Poktan::create($data + [
                'status' => 'draft',
                'diajukan_oleh' => $user->id,
                'jumlah_anggota' => 0,
                'kode_undangan' => Poktan::buatKode(),
            ]);

            // Ketua otomatis menjadi anggota pertama; datanya dilengkapi sendiri
            $poktan->anggota()->create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nik' => $user->nik,
                'no_hp' => $user->no_hp,
                'alamat' => $user->alamat,
                'jabatan' => 'ketua',
                'status' => 'menunggu',
            ]);

            return $poktan;
        });

        return redirect()->route('user.poktan.anggota', $poktan)
            ->with('status', 'Poktan dibuat. Bagikan kode undangan ke anggota, lalu lengkapi data Anda sendiri.');
    }

    public function show(Request $request, Poktan $poktan): View|RedirectResponse
    {
        $saya = $this->barisSaya($poktan);
        $isKetua = $this->adalahKetua($poktan);

        abort_unless($saya || $isKetua || (int) $request->user()->poktan_id === (int) $poktan->id, 403);

        if ($poktan->status === 'draft') {
            return redirect()->route($isKetua ? 'user.poktan.anggota' : 'user.poktan.isi', $poktan);
        }

        $this->muat($poktan);

        return view('user.poktan.show', compact('poktan', 'isKetua', 'saya'));
    }

    // ------------------------------------------------------------------ ketua: kelola anggota

    public function anggota(Request $request, Poktan $poktan): View|RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan), 403);

        if ($poktan->status !== 'draft') {
            return redirect()->route('user.poktan.show', $poktan);
        }

        $this->muat($poktan);
        [$siap, $alasan] = $this->cekKesiapan($poktan);

        return view('user.poktan.anggota', [
            'poktan' => $poktan,
            'siap' => $siap,
            'alasan' => $alasan,
            'saya' => $this->barisSaya($poktan),
            'minLuas' => $this->minLuas(),
            'maksAnggota' => $this->maksAnggota(),
        ]);
    }

    public function kodeBaru(Poktan $poktan): RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan) && $poktan->status === 'draft', 403);

        $poktan->update(['kode_undangan' => Poktan::buatKode()]);

        return back()->with('status', 'Kode undangan diganti. Kode lama tidak berlaku lagi.');
    }

    public function jabatan(Request $request, Poktan $poktan, PoktanAnggota $anggota): RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan) && $poktan->status === 'draft', 403);
        abort_unless((int) $anggota->poktan_id === (int) $poktan->id && $anggota->jabatan !== 'ketua', 403);

        $jabatan = $request->validate([
            'jabatan' => ['required', 'in:sekretaris,bendahara,anggota'],
        ])['jabatan'];

        if ($jabatan !== 'anggota'
            && $poktan->anggota()->where('jabatan', $jabatan)->where('id', '!=', $anggota->id)->exists()) {
            return back()->with('error', 'Jabatan ' . $jabatan . ' sudah dipegang anggota lain.');
        }

        $anggota->update(['jabatan' => $jabatan]);

        return back()->with('status', 'Jabatan ' . $anggota->nama . ' diubah menjadi ' . $jabatan . '.');
    }

    public function anggotaDestroy(Poktan $poktan, PoktanAnggota $anggota): RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan) && $poktan->status === 'draft', 403);
        abort_unless((int) $anggota->poktan_id === (int) $poktan->id && $anggota->jabatan !== 'ketua', 403);

        $nama = $anggota->nama;
        $this->hapusBerkas($anggota);
        $anggota->delete();

        return back()->with('status', $nama . ' dikeluarkan dari poktan.');
    }

    public function selesai(Poktan $poktan): RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan) && $poktan->status === 'draft', 403);

        $this->muat($poktan);
        [$siap, $alasan] = $this->cekKesiapan($poktan);

        if (! $siap) {
            return back()->with('error', implode(' ', $alasan));
        }

        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif');

        $poktan->update([
            'status' => 'diajukan',
            'nama_ketua' => $aktif->firstWhere('jabatan', 'ketua')->nama,
            'jumlah_anggota' => $aktif->count(),
            'luas_lahan_ha' => $aktif->sum('luas_lahan_ha'),
            'dikunci_at' => now(),
            'catatan_admin' => null,
        ]);

        return redirect()->route('user.poktan')
            ->with('status', 'Pengajuan poktan terkirim. Menunggu verifikasi admin UPTD.');
    }

    public function perbaiki(Poktan $poktan): RedirectResponse
    {
        abort_unless($this->adalahKetua($poktan) && $poktan->status === 'ditolak', 403);

        $poktan->update(['status' => 'draft', 'dikunci_at' => null]);

        return redirect()->route('user.poktan.anggota', $poktan)
            ->with('status', 'Pengajuan dibuka kembali. Perbaiki sesuai catatan admin lalu ajukan lagi.');
    }

    // ------------------------------------------------------------------ anggota: gabung & isi data sendiri

    public function gabung(Request $request): View|RedirectResponse
    {
        if ($this->sudahTerikat($request->user())) {
            session()->forget('kode_undangan');

            return redirect()->route('user.poktan')->with('error', 'Anda sudah terdaftar di sebuah poktan.');
        }

        $kode = strtoupper(trim((string) $request->query('kode')));

        // Datang dari tautan undangan: langsung tampilkan konfirmasi tanpa mengetik kode lagi
        if ($kode !== '') {
            [$poktan, $galat] = $this->periksaKode($request->user(), $kode);

            if (! $galat) {
                $poktan->load('desa');

                return view('user.poktan.gabung', [
                    'poktan' => $poktan,
                    'kode' => $kode,
                    'ketuaNama' => $poktan->anggota()->where('jabatan', 'ketua')->value('nama'),
                    'jumlah' => $poktan->anggota()->where('status', '!=', 'nonaktif')->count(),
                    'maksAnggota' => $this->maksAnggota(),
                ]);
            }

            session()->forget('kode_undangan');

            return view('user.poktan.gabung', ['poktan' => null, 'kode' => $kode])
                ->withErrors(['kode' => $galat]);
        }

        return view('user.poktan.gabung', ['poktan' => null, 'kode' => '']);
    }

        public function gabungCek(Request $request): RedirectResponse
    {
        $request->validate(['kode' => ['required', 'string', 'max:20']]);

        return redirect()->route('user.poktan.gabung', [
            'kode' => strtoupper(trim($request->kode)),
        ]);
    }

    public function gabungKonfirmasi(Request $request): RedirectResponse
    {
        $request->validate(['kode' => ['required', 'string', 'max:20']]);
        $user = $request->user();
        $kode = strtoupper(trim($request->kode));

        [$poktan, $galat] = DB::transaction(function () use ($user, $kode) {
            Poktan::where('kode_undangan', $kode)->lockForUpdate()->first(); // cegah dua orang masuk bersamaan melewati batas

            [$p, $galat] = $this->periksaKode($user, $kode);
            if ($galat) {
                return [null, $galat];
            }

            $p->anggota()->create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nik' => $user->nik,
                'no_hp' => $user->no_hp,
                'alamat' => $user->alamat,
                'jabatan' => 'anggota',
                'status' => 'menunggu',
            ]);

            return [$p, null];
        });

        session()->forget('kode_undangan');

        if ($galat) {
            return redirect()->route('user.poktan.gabung')->with('error', $galat);
        }

        return redirect()->route('user.poktan.isi', $poktan)
            ->with('status', 'Anda bergabung ke ' . $poktan->nama_kelompok . '. Lengkapi data diri dan unggah berkas Anda.');
    }

    public function gabungBatal(): RedirectResponse
    {
        session()->forget('kode_undangan');

        return back();
    }

    public function isi(Poktan $poktan): View|RedirectResponse
    {
        $saya = $this->barisSaya($poktan);
        abort_unless($saya, 403);

        if ($poktan->status !== 'draft') {
            return redirect()->route('user.poktan.show', $poktan);
        }

        $poktan->load('desa');

        return view('user.poktan.isi', [
            'poktan' => $poktan,
            'saya' => $saya,
            'isKetua' => $this->adalahKetua($poktan),
        ]);
    }

    public function isiSimpan(Request $request, Poktan $poktan): RedirectResponse
    {
        $saya = $this->barisSaya($poktan);
        abort_unless($saya && $poktan->status === 'draft', 403);

        $berkas = fn (string $f) => [$saya->{$f} ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor_kk' => ['required', 'digits:16'],
            'nama_ibu_kandung' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'luas_lahan_ha' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'file_ktp' => $berkas('file_ktp'),
            'file_kk' => $berkas('file_kk'),
            'file_sppt' => $berkas('file_sppt'),
        ]);

        // NIK mengikuti akun dan tidak bisa diubah
        $data['nik'] = $request->user()->nik;

        $ganda = PoktanAnggota::where('nik', $data['nik'])
            ->where('id', '!=', $saya->id)
            ->where('status', '!=', 'nonaktif')
            ->whereHas('poktan', fn ($q) => $q->whereIn('status', self::STATUS_TERIKAT))
            ->exists();

        if ($ganda) {
            return back()->withErrors(['nama' => 'NIK akun Anda sudah tercatat di poktan lain. Hubungi petugas UPTD.'])->withInput();
        }

        foreach (['file_ktp', 'file_kk', 'file_sppt'] as $f) {
            if ($request->hasFile($f)) {
                if ($saya->{$f}) {
                    Storage::disk('local')->delete($saya->{$f});
                }
                $data[$f] = $request->file($f)->store('poktan/anggota', 'local'); // disimpan privat
            } else {
                unset($data[$f]);
            }
        }

        $saya->fill($data);
        $saya->status = ($saya->file_ktp && $saya->file_kk && $saya->file_sppt) ? 'lengkap' : 'menunggu';
        $saya->save();

        $tujuan = $this->adalahKetua($poktan) ? 'user.poktan.anggota' : 'user.poktan';

        return redirect()->route($tujuan, $tujuan === 'user.poktan.anggota' ? $poktan : [])
            ->with('status', 'Data Anda tersimpan' . ($saya->status === 'lengkap' ? ' dan sudah lengkap.' : ', tetapi berkas belum lengkap.'));
    }

    public function keluar(Poktan $poktan): RedirectResponse
    {
        $saya = $this->barisSaya($poktan);
        abort_unless($saya && $saya->jabatan !== 'ketua' && $poktan->status === 'draft', 403);

        $this->hapusBerkas($saya);
        $saya->delete();

        return redirect()->route('user.poktan')->with('status', 'Anda keluar dari poktan.');
    }

    // Berkas hanya bisa dibuka pemiliknya sendiri
    public function berkas(Request $request, Poktan $poktan, PoktanAnggota $anggota, string $jenis)
    {
        abort_unless(
            in_array($jenis, ['ktp', 'kk', 'sppt'], true)
            && (int) $anggota->poktan_id === (int) $poktan->id
            && (int) $anggota->user_id === (int) $request->user()->id,
            404
        );

        $path = $anggota->{'file_' . $jenis};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    // ------------------------------------------------------------------ pembantu

    private function adalahKetua(Poktan $poktan): bool
    {
        return (int) $poktan->diajukan_oleh === (int) auth()->id();
    }

    private function barisSaya(Poktan $poktan): ?PoktanAnggota
    {
        return $poktan->anggota()->where('user_id', auth()->id())->first();
    }

    private function poktanSaya($user): ?Poktan
    {
        $baris = PoktanAnggota::where('user_id', $user->id)
            ->whereHas('poktan', fn ($q) => $q->whereIn('status', self::STATUS_TERIKAT))
            ->latest('id')->first();

        if ($baris) {
            return Poktan::with('desa')->find($baris->poktan_id);
        }

        // data lama / akun yang dihubungkan admin
        return $user->poktan_id ? Poktan::with('desa')->find($user->poktan_id) : null;
    }

    private function sudahTerikat($user): bool
    {
        if ($user->poktan_id && Poktan::where('id', $user->poktan_id)->where('status', 'disetujui')->exists()) {
            return true;
        }

        return PoktanAnggota::where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->nik) {
                    $q->orWhere('nik', $user->nik);
                }
            })
            ->where('status', '!=', 'nonaktif')
            ->whereHas('poktan', fn ($q) => $q->whereIn('status', self::STATUS_TERIKAT))
            ->exists();
    }

    private function halangan(Request $request): ?RedirectResponse
    {
        if ($this->sudahTerikat($request->user())) {
            return redirect()->route('user.poktan')->with('error', 'Anda sudah memiliki atau tergabung dalam sebuah poktan.');
        }

        return null;
    }

    private function periksaKode($user, string $kode): array
    {
        if ($this->sudahTerikat($user)) {
            return [null, 'Anda sudah terdaftar di sebuah poktan.'];
        }

        $poktan = Poktan::where('kode_undangan', $kode)->first();

        if (! $poktan) {
            return [null, 'Kode tidak ditemukan. Periksa kembali dengan ketua poktan.'];
        }
        if ($poktan->status !== 'draft' || $poktan->dikunci_at) {
            return [null, 'Poktan ini sudah tidak menerima anggota baru.'];
        }
        if ($poktan->anggota()->where('status', '!=', 'nonaktif')->count() >= $this->maksAnggota()) {
            return [null, 'Jumlah anggota poktan ini sudah penuh.'];
        }

        return [$poktan, null];
    }

    private function muat(Poktan $poktan): void
    {
        $poktan->load([
            'desa',
            'anggota' => fn ($q) => $q
                ->orderByRaw("FIELD(jabatan, 'ketua', 'sekretaris', 'bendahara', 'anggota')")
                ->orderBy('nama'),
        ]);
    }

    private function hapusBerkas(PoktanAnggota $a): void
    {
        Storage::disk('local')->delete(array_filter([$a->file_ktp, $a->file_kk, $a->file_sppt]));
    }

    private function cekKesiapan(Poktan $poktan): array
    {
        $alasan = [];
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif');

        if ($aktif->count() < $this->minAnggota()) {
            $alasan[] = 'Minimal ' . $this->minAnggota() . ' anggota.';
        }
        if ($aktif->count() > $this->maksAnggota()) {
            $alasan[] = 'Anggota melebihi batas ' . $this->maksAnggota() . ' orang.';
        }
        if ($aktif->where('jabatan', 'ketua')->count() !== 1) {
            $alasan[] = 'Harus ada tepat 1 ketua.';
        }

        $belum = $aktif->where('status', '!=', 'lengkap');
        if ($belum->isNotEmpty()) {
            $alasan[] = 'Menunggu data lengkap dari: ' . $belum->pluck('nama')->filter()->implode(', ') . '.';
        }

        $total = (float) $aktif->sum('luas_lahan_ha');
        $min = $this->minLuas();
        if ($total < $min) {
            $alasan[] = 'Total luas lahan ' . $fmt($total) . ' ha, kurang ' . $fmt($min - $total) . ' ha dari minimal ' . $fmt($min) . ' ha.';
        }

        $desa = $poktan->desa;
        if (! $desa) {
            $alasan[] = 'Desa belum dipilih.';
        } elseif ($desa->batasHa() !== null && $total > $desa->sisaHa()) {
            $alasan[] = 'Total luas lahan melebihi sisa batas luas sawah desa ' . $desa->nama . ' (' . $fmt($desa->sisaHa()) . ' ha).';
        }

        return [empty($alasan), $alasan];
    }
}