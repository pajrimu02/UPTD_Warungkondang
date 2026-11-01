<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\PoktanAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PoktanController extends Controller
{
    public function index(Request $request): View
    {
        $poktans = Poktan::with('desa', 'pengaju')->withCount('anggota')
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->status),
                fn ($q) => $q->where('status', '!=', 'draft')
            )
            ->orderByRaw("FIELD(status, 'diajukan', 'disetujui', 'ditolak', 'draft')")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.poktan.index', compact('poktans'));
    }

    public function show(Poktan $poktan): View
    {
        $poktan->load([
            'desa', 'pengaju',
            'anggota' => fn ($q) => $q->orderByRaw("FIELD(jabatan, 'ketua', 'sekretaris', 'bendahara', 'anggota')")->orderBy('nama'),
        ]);

        return view('admin.poktan.show', [
            'poktan' => $poktan,
            'totalLuas' => (float) $poktan->anggota->sum('luas_lahan_ha'),
            'sisaDesa' => ($poktan->desa && $poktan->desa->batasHa() !== null) ? $poktan->desa->sisaHa() : null,
        ]);
    }

    public function update(Request $request, Poktan $poktan): RedirectResponse
    {
        abort_unless($poktan->status === 'diajukan', 403);

        $data = $request->validate([
            'keputusan' => ['required', 'in:disetujui,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['keputusan'] === 'ditolak' && blank($data['catatan_admin'] ?? null)) {
            return back()->withErrors(['catatan_admin' => 'Isi alasan penolakan.'])->withInput();
        }

        if ($data['keputusan'] === 'disetujui') {
            $poktan->load('desa', 'anggota');
            $total = (float) $poktan->anggota->sum('luas_lahan_ha');
            $batasPoktan = (float) config('uptd.maks_luas_poktan_ha', 25);

            if (! $poktan->desa) {
                return back()->with('error', 'Desa poktan belum dipilih.');
            }
            if ($total > $batasPoktan) {
                return back()->with('error', 'Total luas lahan melebihi batas ' . $batasPoktan . ' ha per poktan.');
            }
            if ($poktan->desa->batasHa() !== null && $total > $poktan->desa->sisaHa()) {
                return back()->with('error', 'Total luas lahan melebihi sisa batas luas sawah desa.');
            }
        }

        $poktan->update([
            'status' => $data['keputusan'],
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'disetujui_at' => $data['keputusan'] === 'disetujui' ? now() : null,
        ]);

        return redirect()->route('admin.poktan.show', $poktan)
            ->with('success', 'Poktan "' . $poktan->nama_kelompok . '" ' . $data['keputusan'] . '.');
    }

    public function berkas(Poktan $poktan, PoktanAnggota $anggota, string $jenis)
    {
        abort_unless(in_array($jenis, ['ktp', 'kk', 'sppt'], true) && (int) $anggota->poktan_id === (int) $poktan->id, 404);

        $path = $anggota->{'file_' . $jenis};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}