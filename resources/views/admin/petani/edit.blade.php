@extends('layouts.admin')

@section('title', 'Edit Akun Petani')

@section('content')
    <a href="{{ route('admin.petani.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Edit Data Petani</h1>

    <form method="POST" action="{{ route('admin.petani.update', $petani) }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $petani->name) }}" class="w-full rounded-lg border-[#E1DCC9]">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $petani->email) }}" class="w-full rounded-lg border-[#E1DCC9]">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">NIK</label>
                <input type="text" name="nik" maxlength="16" value="{{ old('nik', $petani->nik) }}" class="w-full rounded-lg border-[#E1DCC9]">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $petani->no_hp) }}" class="w-full rounded-lg border-[#E1DCC9]">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.petani.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">Simpan</button>
        </div>
    </form>
@endsection