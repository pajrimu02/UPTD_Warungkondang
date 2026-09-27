<?php
// app/Http/Controllers/Admin/PetaniController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PetaniController extends Controller
{
    public function index(Request $request): View
    {
        $petanis = User::where('role', 'user')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('nik', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.petani.index', compact('petanis'));
    }

    public function edit(User $user): View
    {
        abort_unless($user->role === 'user', 404);

        return view('admin.petani.edit', ['petani' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nik' => ['nullable', 'digits:16'],
            'no_hp' => ['nullable', 'string', 'max:15'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.petani.index')
            ->with('success', 'Data petani berhasil diperbarui.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);

        $newPassword = Str::random(8);
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()
            ->route('admin.petani.index')
            ->with('success', "Password {$user->name} berhasil di-reset. Password baru: {$newPassword}");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->role === 'user', 404);

        $user->delete();

        return redirect()
            ->route('admin.petani.index')
            ->with('success', 'Akun petani berhasil dihapus.');
    }
}