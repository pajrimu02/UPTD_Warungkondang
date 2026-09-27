@extends('layouts.user')

@section('title', 'Edit Profil')

@section('content')

    <a href="{{ route('user.profil') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Edit Profil</h1>

    <form method="POST" action="{{ route('user.profil.update') }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-8">
        @csrf
        @method('PUT')

        {{-- Data diri --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-person-vcard"></i>
                <span>Data Diri</span>
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">NIK</label>
                        <input type="text" name="nik" maxlength="16" value="{{ old('nik', $user->nik) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Ganti password --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-1">
                <i class="bi bi-shield-lock"></i>
                <span>Ganti Password</span>
            </p>
            <p class="text-sm text-[#5C6B62] mb-4">Kosongkan kalau tidak ingin mengganti password.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('user.profil') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">
                <i class="bi bi-check-lg"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

@endsection