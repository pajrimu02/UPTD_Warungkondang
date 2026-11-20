@extends('layouts.admin')

@section('title', 'Edit Akun Petani')

@section('content')

    @php $input = 'w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm'; @endphp

    <a href="{{ route('admin.petani.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Edit Akun Petani</h1>

    <form method="POST" action="{{ route('admin.petani.update', $petani) }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5 max-w-3xl">
        @csrf @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ([
                ['name', 'Nama Lengkap', 'text'], ['email', 'Email', 'email'],
                ['nik', 'NIK', 'text'], ['no_hp', 'No. HP', 'text'],
            ] as [$f, $label, $tipe])
                <div>
                    <label class="block text-sm mb-1">{{ $label }}</label>
                    <input type="{{ $tipe }}" name="{{ $f }}" value="{{ old($f, $petani->$f) }}" @if ($f === 'nik') maxlength="16" inputmode="numeric" @endif class="{{ $input }}">
                    @error($f) <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div class="sm:col-span-2">
                <label class="block text-sm mb-1">Alamat</label>
                <textarea name="alamat" rows="2" class="{{ $input }}">{{ old('alamat', $petani->alamat) }}</textarea>
                @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">Konsumen Pengguna</label>
                <select name="konsumen_pengguna" class="{{ $input }} bg-white">
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
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm mb-1">Nama Kapal <span class="text-[#5C6B62] font-normal">(untuk usaha perikanan atau motor tempel)</span></label>
                <input type="text" name="nama_kapal" value="{{ old('nama_kapal', $petani->nama_kapal) }}" class="{{ $input }}">
            </div>
        </div>

        <div class="rounded-xl bg-[#F5F3EC] p-4">
            <label class="block text-sm mb-1 font-medium text-[#1B4332]">Poktan</label>
            <select name="poktan_id" class="{{ $input }} bg-white">
                <option value="">-- Tidak tergabung --</option>
                @foreach ($poktans as $pk)
                    <option value="{{ $pk->id }}" @selected((int) old('poktan_id', $petani->poktan_id) === $pk->id)>{{ $pk->nama_kelompok }}</option>
                @endforeach
            </select>
            @error('poktan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-[#5C6B62] mt-2">Hanya poktan yang sudah disetujui. Keanggotaan normal terhubung otomatis saat poktan disetujui. Gunakan pilihan ini hanya untuk koreksi.</p>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.petani.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">Simpan Perubahan</button>
        </div>
    </form>

@endsection