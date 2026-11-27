<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\SuratPupuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuratPupukController extends Controller
{
    private function tolak(): RedirectResponse
    {
        return redirect()->route('user.surat-pengajuan.index')->with(
            'error',
            'Surat pupuk hanya bisa diajukan oleh pengaju poktan atau ketua poktan yang sudah disetujui. Pastikan NIK di profil sama dengan NIK ketua.'
        );
    }

    private function penyuluhUntukPoktan(Poktan $poktan): ?array
    {
        $poktan->loadMissing('desa');
        $namaDesa = trim((string) ($poktan->desa?->nama_desa ?? $poktan->desa?->nama ?? ''));

        if ($namaDesa === '') {
            return null;
        }

        return collect(config('uptd.penyuluh', []))->first(function ($ppl) use ($namaDesa) {
            $wilayah = array_map(fn ($d) => mb_strtolower(trim($d)), $ppl['wilayah_desa'] ?? []);

            return in_array(mb_strtolower($namaDesa), $wilayah, true);
        });
    }

    public function index(Request $request): View
    {
        $suratPupuks = SuratPupuk::withCount('items')
            ->where('user_id', $request->user()->id)->latest()->paginate(10);

        return view('user.surat-pupuk.index', compact('suratPupuks'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $poktan = $request->user()->poktanPengelola();
        if (! $poktan) {
            return $this->tolak();
        }

        $poktan->load([
            'desa',
            'anggota' => fn ($q) => $q->where('status', '!=', 'nonaktif')->orderBy('nama'),
        ]);

        if ($poktan->anggota->isEmpty()) {
            return redirect()->route('user.surat-pengajuan.index')->with('error', 'Poktan belum punya anggota aktif.');
        }

        $penyuluhOtomatis = $this->penyuluhUntukPoktan($poktan);
        $penyuluhList = config('uptd.penyuluh', []);
        $kiosList = config('kios_pupuk', []);

        return view('user.surat-pupuk.create', compact('poktan', 'penyuluhOtomatis', 'penyuluhList', 'kiosList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $poktan = $request->user()->poktanPengelola();
        if (! $poktan) {
            return $this->tolak();
        }

        $anggotaIds = $poktan->anggota()
            ->where('status', '!=', 'nonaktif')
            ->pluck('id')
            ->map(fn ($i) => (string) $i)
            ->all();

        $items = collect($request->input('items', []))
            ->filter(fn ($row, $id) => ! empty($row['include']) && in_array((string) $id, $anggotaIds, true))
            ->all();
        $request->merge(['items' => $items]);

        $penyuluhOtomatis = $this->penyuluhUntukPoktan($poktan);
        if ($penyuluhOtomatis) {
            $request->merge(['nama_penyuluh' => $penyuluhOtomatis['nama']]);
        }

        $validPenyuluh = collect(config('uptd.penyuluh', []))->pluck('nama')->all();
        $kiosList = collect(config('kios_pupuk', []));

        $data = $request->validate([
            'nama_penyuluh' => ['required', 'string', 'max:255', Rule::in($validPenyuluh)],
            'kode_desa' => 'nullable|string|max:20',
            'kode_kios' => 'nullable|string|max:255',
            'nama_kios' => ['nullable', 'string', 'max:255', Rule::in($kiosList->pluck('nama')->all())],
            'gapoktan' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',
            'items.*.nama_petani' => 'required|string|max:255',
            'items.*.nik' => 'required|digits:16',
            'items.*.tempat_lahir' => 'required|string|max:255',
            'items.*.tanggal_lahir' => 'required|date|before:today',
            'items.*.nama_ibu_kandung' => 'required|string|max:255',
            'items.*.alamat' => 'required|string|max:500',
            'items.*.subsektor' => ['required', 'in:Tanaman Pangan,Hortikultura,Perkebunan,Peternakan,Petambak'],
            'items.*.musim' => 'required|array|size:3',
            'items.*.musim.*.komoditas' => 'nullable|string|max:100',
            'items.*.musim.*.luas' => 'nullable|numeric|min:0',
            'items.*.musim.*.urea' => 'nullable|numeric|min:0',
            'items.*.musim.*.npk' => 'nullable|numeric|min:0',
            'items.*.musim.*.npk_formula' => 'nullable|numeric|min:0',
            'items.*.musim.*.organik' => 'nullable|numeric|min:0',
            'items.*.musim.*.za' => 'nullable|numeric|min:0',
        ], [
            'items.required' => 'Pilih minimal satu petani.',
            'items.*.nik.digits' => 'NIK harus 16 digit angka.',
        ]);

        $kiosDipilih = $kiosList->firstWhere('nama', $data['nama_kios'] ?? null);
        $data['kode_kios'] = $kiosDipilih['kode'] ?? null;

        $surat = DB::transaction(function () use ($data, $poktan, $request) {
            $surat = SuratPupuk::create([
                'user_id' => $request->user()->id,
                'poktan_id' => $poktan->id,
                'nama_penyuluh' => $data['nama_penyuluh'],
                'kode_desa' => $data['kode_desa'] ?? null,
                'kode_kios' => $data['kode_kios'] ?? null,
                'nama_kios' => $data['nama_kios'] ?? null,
                'gapoktan' => $data['gapoktan'] ?? null,
                'status' => 'diajukan',
            ]);

            foreach ($data['items'] as $anggotaId => $row) {
                $musim = collect($row['musim'])->values()->map(fn ($m) => [
                    'komoditas' => ($m['komoditas'] ?? '') !== '' ? $m['komoditas'] : '-',
                    'luas' => (float) ($m['luas'] ?? 0),
                    'urea' => (float) ($m['urea'] ?? 0),
                    'npk' => (float) ($m['npk'] ?? 0),
                    'npk_formula' => (float) ($m['npk_formula'] ?? 0),
                    'organik' => (float) ($m['organik'] ?? 0),
                    'za' => (float) ($m['za'] ?? 0),
                ])->all();

                $surat->items()->create(array_merge($row, [
                    'poktan_anggota_id' => $anggotaId,
                    'musim' => $musim,
                ]));

                $poktan->anggota()->whereKey($anggotaId)->update([
                    'tempat_lahir' => $row['tempat_lahir'],
                    'tanggal_lahir' => $row['tanggal_lahir'],
                    'nama_ibu_kandung' => $row['nama_ibu_kandung'],
                ]);
            }

            return $surat;
        });

        return redirect()->route('user.surat-pupuk.show', $surat)->with('status', 'Pengajuan pupuk berhasil dikirim.');
    }

    public function show(SuratPupuk $surat_pupuk): View
    {
        abort_unless($surat_pupuk->user_id === auth()->id(), 403);
        $surat_pupuk->load('poktan', 'items');

        return view('user.surat-pupuk.show', ['data' => $surat_pupuk]);
    }

    public function resmi(SuratPupuk $surat_pupuk): View
    {
        abort_unless($surat_pupuk->user_id === auth()->id(), 403);
        $surat_pupuk->load('disetujuiOleh');

        return view('user.surat-pupuk.resmi', ['data' => $surat_pupuk]);
    }
}