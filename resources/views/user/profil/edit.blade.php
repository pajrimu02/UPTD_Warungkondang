@extends('layouts.user')

@section('title', 'Edit Profil')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332]">Edit Profil</h2>
        <a href="{{ route('user.profil') }}" class="text-sm text-[#5C6B62]">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('user.profil.update') }}" class="bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">NIK</label>
                <input type="text" name="nik" maxlength="16" value="{{ old('nik', $user->nik) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        <p class="text-sm text-[#5C6B62]">Kosongkan bagian di bawah kalau tidak ingin ganti password.</p>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Password Baru</label>
                <input type="password" name="password" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('user.profil') }}" class="text-sm text-[#5C6B62] px-4 py-2">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2 rounded-full">Simpan Perubahan</button>
        </div>
    </form>
@endsection