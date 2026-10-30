<?php 
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\SuratPupuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $poktan->load(['desa', 'anggota' => fn ($q) => $q->orderBy('nama')]);

        if ($poktan->anggota->isEmpty()) {
            return redirect()->route('user.surat-pengajuan.index')->with('error', 'Poktan belum punya anggota.');
        }

        return view('user.surat-pupuk.create', compact('poktan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $poktan = $request->user()->poktanPengelola();
        if (! $poktan) {
            return $this->tolak();
        }

        $anggotaIds = $poktan->anggota()->pluck('id')->map(fn ($i) => (string) $i)->all();

        // hanya petani yang dicentang dan benar-benar anggota poktan ini
        $items = collect($request->input('items', []))
            ->filter(fn ($row, $id) => ! empty($row['include']) && in_array((string) $id, $anggotaIds, true))
            ->all();
        $request->merge(['items' => $items]);

        $data = $request->validate([
            'nama_penyuluh' => 'required|string|max:255',
            'kode_desa' => 'nullable|string|max:20',
            'kode_kios' => 'nullable|string|max:255',
            'nama_kios' => 'nullable|string|max:255',
            'gapoktan' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',
            'items.*.nama_petani' => 'required|string|max:255',
            'items.*.nik' => 'required|digits:16',
            'items.*.tempat_lahir' => 'required|string|max:255',
            'items.*.tanggal_lahir' => 'required|date|before:today',
            'items.*.nama_ibu_kandung' => 'required|string|max:255',
            'items.*.alamat' => 'required|string|max:500',
            'items.*.subsektor' => 'required|string|max:255',
            'items.*.musim' => 'required|array|size:3',
            'items.*.musim.*.komoditas' => 'nullable|string|max:100',
            'items.*.musim.*.luas' => 'nullable|numeric|min:0',
            'items.*.musim.*.urea' => 'nullable|numeric|min:0',
            'items.*.musim.*.sp36' => 'nullable|numeric|min:0',
            'items.*.musim.*.za' => 'nullable|numeric|min:0',
            'items.*.musim.*.npk' => 'nullable|numeric|min:0',
            'items.*.musim.*.organik' => 'nullable|numeric|min:0',
        ], [
            'items.required' => 'Pilih minimal satu petani.',
            'items.*.nik.digits' => 'NIK harus 16 digit angka.',
        ]);

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
                    'sp36' => (float) ($m['sp36'] ?? 0),
                    'za' => (float) ($m['za'] ?? 0),
                    'npk' => (float) ($m['npk'] ?? 0),
                    'organik' => (float) ($m['organik'] ?? 0),
                ])->all();

                // array_merge: nilai di array kedua menimpa $row (musim sudah dibersihkan)
                $surat->items()->create(array_merge($row, [
                    'poktan_anggota_id' => $anggotaId,
                    'musim' => $musim,
                ]));

                // simpan biodata ke anggota supaya pengajuan berikutnya terisi otomatis
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