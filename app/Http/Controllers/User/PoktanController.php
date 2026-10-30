<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Poktan;
use App\Models\PoktanAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PoktanController extends Controller
{
    // TODO: konfirmasi jumlah minimal anggota resmi ke UPTD
    private const MIN_ANGGOTA = 1;

    public function index(Request $request): View
    {
        $user = $request->user();

        $poktan = Poktan::with('desa')->withCount('anggota')
            ->where('diajukan_oleh', $user->id)->latest()->first();

        if (! $poktan && $user->poktan_id) {
            $poktan = Poktan::with('desa')->withCount('anggota')->find($user->poktan_id);
        }

        return view('user.poktan.index', compact('poktan'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($stop = $this->halangan($request)) {
            return $stop;
        }

        $desas = Desa::orderBy('nama')->get();

        return view('user.poktan.create', compact('desas'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($stop = $this->halangan($request)) {
            return $stop;
        }

        $data = $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:255',
                Rule::unique('poktans', 'nama_kelompok')->where(fn ($q) => $q->whereIn('status', ['diajukan', 'disetujui']))],
            'desa_id' => ['required', 'exists:desas,id'],
            'alamat' => ['required', 'string', 'max:500'],
        ]);

        $poktan = Poktan::create($data + [
            'status' => 'draft',
            'diajukan_oleh' => $request->user()->id,
            'jumlah_anggota' => 0,
        ]);

        return redirect()->route('user.poktan.anggota', $poktan)
            ->with('status', 'Data poktan tersimpan. Sekarang tambahkan anggota.');
    }

    public function show(Request $request, Poktan $poktan): View|RedirectResponse
    {
        $user = $request->user();
        $pemilik = (int) $poktan->diajukan_oleh === (int) $user->id;
        abort_unless($pemilik || (int) $user->poktan_id === (int) $poktan->id, 403);

        if ($poktan->status === 'draft') {
            return redirect()->route('user.poktan.anggota', $poktan);
        }

        $this->muat($poktan);

        return view('user.poktan.show', compact('poktan', 'pemilik'));
    }

    public function anggota(Request $request, Poktan $poktan): View|RedirectResponse
    {
        $this->milik($poktan);

        if ($poktan->status !== 'draft') {
            return redirect()->route('user.poktan.show', $poktan);
        }

        $this->muat($poktan);
        [$siap, $alasan] = $this->cekKesiapan($poktan);

        return view('user.poktan.anggota', compact('poktan', 'siap', 'alasan'));
    }

    public function anggotaStore(Request $request, Poktan $poktan): RedirectResponse
    {
        $this->milik($poktan);
        abort_unless($poktan->status === 'draft', 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'luas_lahan_ha' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'jabatan' => ['required', 'in:ketua,sekretaris,bendahara,anggota'],
            'file_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_kk' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_sppt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'nama_ibu_kandung' => ['required', 'string', 'max:255'],    
        ]);

        if ($poktan->anggota()->where('nik', $data['nik'])->exists()) {
            return back()->withErrors(['nik' => 'NIK ini sudah ada di daftar anggota.'])->withInput();
        }

        $dipakai = PoktanAnggota::where('nik', $data['nik'])
            ->whereHas('poktan', fn ($q) => $q->whereIn('status', ['diajukan', 'disetujui'])->where('id', '!=', $poktan->id))
            ->exists();
        if ($dipakai) {
            return back()->withErrors(['nik' => 'NIK ini sudah terdaftar sebagai anggota poktan lain.'])->withInput();
        }

        if ($data['jabatan'] === 'ketua' && $poktan->anggota()->where('jabatan', 'ketua')->exists()) {
            return back()->withErrors(['jabatan' => 'Ketua sudah ada. Pilih jabatan lain.'])->withInput();
        }

        // Berkas identitas disimpan PRIVAT (tidak bisa dibuka lewat URL publik)
        foreach (['file_ktp', 'file_kk', 'file_sppt'] as $field) {
            $data[$field] = $request->file($field)->store('poktan/anggota', 'local');
        }

        $poktan->anggota()->create($data);

        return back()->with('status', 'Anggota "' . $data['nama'] . '" ditambahkan.');
    }

    public function anggotaDestroy(Request $request, Poktan $poktan, PoktanAnggota $anggota): RedirectResponse
    {
        $this->milik($poktan);
        abort_unless($poktan->status === 'draft' && (int) $anggota->poktan_id === (int) $poktan->id, 403);

        Storage::disk('local')->delete(array_filter([$anggota->file_ktp, $anggota->file_kk, $anggota->file_sppt]));
        $anggota->delete();

        return back()->with('status', 'Anggota dihapus.');
    }

    public function selesai(Request $request, Poktan $poktan): RedirectResponse
    {
        $this->milik($poktan);
        abort_unless($poktan->status === 'draft', 403);

        $this->muat($poktan);
        [$siap, $alasan] = $this->cekKesiapan($poktan);

        if (! $siap) {
            return back()->with('error', implode(' ', $alasan));
        }

        $ketua = $poktan->anggota->firstWhere('jabatan', 'ketua');

        $poktan->update([
            'status' => 'diajukan',
            'nama_ketua' => $ketua->nama,
            'jumlah_anggota' => $poktan->anggota->count(),
            'luas_lahan_ha' => $poktan->anggota->sum('luas_lahan_ha'),
            'catatan_admin' => null,
        ]);

        return redirect()->route('user.poktan')
            ->with('status', 'Pengajuan poktan terkirim. Menunggu verifikasi admin UPTD.');
    }

    public function berkas(Request $request, Poktan $poktan, PoktanAnggota $anggota, string $jenis)
    {
        $this->milik($poktan);
        abort_unless(in_array($jenis, ['ktp', 'kk', 'sppt'], true) && (int) $anggota->poktan_id === (int) $poktan->id, 404);

        $path = $anggota->{'file_' . $jenis};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    // ------------------------------------------------------------------

    private function milik(Poktan $poktan): void
    {
        abort_unless((int) $poktan->diajukan_oleh === (int) auth()->id(), 403);
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

    private function halangan(Request $request): ?RedirectResponse
    {
        $user = $request->user();

        $draft = Poktan::where('diajukan_oleh', $user->id)->where('status', 'draft')->latest()->first();
        if ($draft) {
            return redirect()->route('user.poktan.anggota', $draft)
                ->with('status', 'Lanjutkan pengisian pengajuan poktan Anda.');
        }

        $aktif = Poktan::where('diajukan_oleh', $user->id)->whereIn('status', ['diajukan', 'disetujui'])->exists();
        $tergabung = $user->poktan_id && Poktan::where('id', $user->poktan_id)->where('status', 'disetujui')->exists();

        if ($aktif || $tergabung) {
            return redirect()->route('user.poktan')
                ->with('error', 'Anda sudah memiliki atau tergabung dalam sebuah poktan.');
        }

        return null;
    }

    private function cekKesiapan(Poktan $poktan): array
    {
        $alasan = [];
        $anggota = $poktan->anggota;
        $fmt = fn ($n) => number_format($n, 2, ',', '.');

        if ($anggota->count() < self::MIN_ANGGOTA) {
            $alasan[] = 'Tambahkan minimal ' . self::MIN_ANGGOTA . ' anggota.';
        }
        if ($anggota->where('jabatan', 'ketua')->count() !== 1) {
            $alasan[] = 'Harus ada tepat 1 ketua.';
        }

        $desa = $poktan->desa;
        if (! $desa) {
            $alasan[] = 'Desa belum dipilih.';
        } elseif ((float) $desa->luas_baku_sawah_ha <= 0) {
            $alasan[] = 'Luas baku sawah desa ' . $desa->nama . ' belum diisi admin. Hubungi petugas UPTD.';
        } else {
            $total = (float) $anggota->sum('luas_lahan_ha');
            $sisa = $desa->sisaHa();
            if ($total > $sisa) {
                $alasan[] = 'Total luas lahan (' . $fmt($total) . ' ha) melebihi sisa luas baku sawah desa '
                    . $desa->nama . ' (' . $fmt($sisa) . ' ha).';
            }
        }

        return [empty($alasan), $alasan];
    }
}