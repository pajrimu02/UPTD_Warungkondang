@extends('layouts.user')

@section('title', 'Ajukan Surat Solar')

@section('content')
    <h2 class="font-semibold text-xl text-[#1B4332] mb-6">Ajukan Surat Rekomendasi Solar</h2>

    <form method="POST" action="{{ route('user.surat-solar.store') }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama Kelompok Tani (opsional)</label>
            <input type="text" name="nama_kelompok_tani" value="{{ old('nama_kelompok_tani') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            @error('nama_kelompok_tani') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">NIK</label>
            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Alamat</label>
            <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Luas Lahan (hektar)</label>
                <input type="number" step="0.01" name="luas_lahan" value="{{ old('luas_lahan') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                @error('luas_lahan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah Liter Diajukan</label>
                <input type="number" name="jumlah_liter_diajukan" value="{{ old('jumlah_liter_diajukan') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                @error('jumlah_liter_diajukan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Keperluan</label>
            <textarea name="keperluan" rows="3" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">{{ old('keperluan') }}</textarea>
            @error('keperluan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">File Pendukung (KTP/foto lahan, opsional)</label>
            <input type="file" name="file_pendukung" class="w-full text-sm">
            @error('file_pendukung') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('user.surat-solar.index') }}" class="text-sm text-[#5C6B62] px-4 py-2">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2 rounded-full">Kirim Pengajuan</button>
        </div>
    </form>
@endsection