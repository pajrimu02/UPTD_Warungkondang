@extends('layouts.admin')

@section('title', 'Edit Akun Petani')

@section('content')
    <a href="{{ route('admin.petani.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Edit Data Petani</h1>

    @php
        $input = 'w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]';
    @endphp

    <form method="POST" action="{{ route('admin.petani.update', $petani) }}"
          x-data="{ konsumen: '{{ old('konsumen_pengguna', $petani->konsumen_pengguna) }}' }"
          class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $petani->name) }}" class="{{ $input }}">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $petani->email) }}" class="{{ $input }}">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">NIK</label>
                <input type="text" name="nik" maxlength="16" inputmode="numeric" value="{{ old('nik', $petani->nik) }}" class="{{ $input }}">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $petani->no_hp) }}" class="{{ $input }}">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Alamat</label>
            <textarea name="alamat" rows="2" class="{{ $input }}">{{ old('alamat', $petani->alamat) }}</textarea>
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <label class="block text-sm mb-1">Kelompok Tani (Poktan)</label>
            <select name="poktan_id" class="{{ $input }} bg-white">
                <option value="">-- Pilih Poktan --</option>
                @foreach ($poktans as $poktan)
                    <option value="{{ $poktan->id }}" @selected(old('poktan_id', $petani->poktan_id) == $poktan->id)>{{ $poktan->nama_kelompok }}</option>
                @endforeach
            </select>
            @error('poktan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Konsumen Pengguna</label>
            <select name="konsumen_pengguna" x-model="konsumen" class="{{ $input }} bg-white">
                <option value="">-- Pilih --</option>
                @foreach ($konsumen as $key => $text)
                    <option value="{{ $key }}" @selected(old('konsumen_pengguna', $petani->konsumen_pengguna) === $key)>{{ $text }}</option>
                @endforeach
            </select>
            @error('konsumen_pengguna') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Jenis Usaha</label>
            <input type="text" name="jenis_usaha" value="{{ old('jenis_usaha', $petani->jenis_usaha) }}" class="{{ $input }}">
            @error('jenis_usaha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="['usaha_perikanan','transportasi_motor_tempel'].includes(konsumen)" x-cloak>
            <label class="block text-sm mb-1">Nama Kapal</label>
            <input type="text" name="nama_kapal" value="{{ old('nama_kapal', $petani->nama_kapal) }}" class="{{ $input }}">
            @error('nama_kapal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.petani.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">Simpan</button>
        </div>
    </form>
@endsection