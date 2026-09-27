<?php
// app/Http/Controllers/User/ProfilController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        return view('user.profil.show', ['user' => auth()->user()]);
    }

    public function edit(): View
    {
        return view('user.profil.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nik' => ['nullable', 'digits:16'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nik = $validated['nik'] ?? null;
        $user->no_hp = $validated['no_hp'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('user.profil')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}