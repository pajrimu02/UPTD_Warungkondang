<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Controller;
use App\Models\Poktan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        return view('user.profil.show', ['user' => auth()->user()->load('poktan')]);
    }

    public function edit(): View
    {
        return view('user.profil.edit', [
            'user'     => auth()->user(),
            'poktans'  => Poktan::orderBy('nama_kelompok')->get(),
            'konsumen' => RegisteredUserController::KONSUMEN,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nik'               => ['nullable', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'no_hp'             => ['nullable', 'string', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'alamat'            => ['nullable', 'string', 'max:500'],
            'konsumen_pengguna' => ['nullable', Rule::in(array_keys(RegisteredUserController::KONSUMEN))],
            'jenis_usaha'       => ['nullable', 'string', 'max:255'],
            'nama_kapal'        => ['nullable', 'string', 'max:255'],
            'poktan_id'         => ['nullable', 'exists:poktans,id'],
            'password'          => ['nullable', 'confirmed', 'min:8'],
        ], [
            'nik.digits'  => 'NIK harus 16 digit angka.',
            'nik.unique'  => 'NIK sudah dipakai akun lain.',
            'no_hp.regex' => 'Format No. HP tidak valid (contoh: 081234567890).',
        ]);

        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->nik               = $validated['nik'] ?? null;
        $user->no_hp             = $validated['no_hp'] ?? null;
        $user->alamat            = $validated['alamat'] ?? null;
        $user->konsumen_pengguna = $validated['konsumen_pengguna'] ?? null;
        $user->jenis_usaha       = $validated['jenis_usaha'] ?? null;
        $user->nama_kapal        = $validated['nama_kapal'] ?? null;
        $user->poktan_id         = $validated['poktan_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('user.profil')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}