<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DesaController extends Controller
{
    public function index(): View
    {
        $desas = Desa::withCount('poktans')->orderBy('nama')->get();

        return view('admin.desa.index', compact('desas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:desas,nama'],
            'kode_desa' => ['nullable', 'string', 'max:20'],
            'luas_baku_sawah_ha' => ['required', 'numeric', 'min:0'],
        ]);

        Desa::create($data);

        return back()->with('success', 'Desa "' . $data['nama'] . '" ditambahkan.');
    }

    public function update(Request $request, Desa $desa): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('desas', 'nama')->ignore($desa->id)],
            'kode_desa' => ['nullable', 'string', 'max:20'],
            'luas_baku_sawah_ha' => ['required', 'numeric', 'min:0'],
        ]);

        $desa->update($data);

        return back()->with('success', 'Desa "' . $desa->nama . '" diperbarui.');
    }

    public function destroy(Desa $desa): RedirectResponse
    {
        if ($desa->poktans()->exists()) {
            return back()->with('error', 'Desa tidak bisa dihapus karena sudah dipakai poktan.');
        }

        $desa->delete();

        return back()->with('success', 'Desa dihapus.');
    }
}