@extends('layouts.user')

@section('title', 'Ajukan Surat Solar')

@section('content')

    <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Ajukan Surat Rekomendasi Solar</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Lengkapi data di bawah ini. Semua kolom bertanda * wajib diisi.</p>

    <form method="POST" action="{{ route('user.surat-solar.store') }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-8">
        @csrf

        {{-- Data pemohon --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-person-vcard"></i>
                <span>Data Pemohon</span>
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Nama Kelompok Tani <span class="text-[#5C6B62]">(opsional)</span></label>
                    <input type="text" name="nama_kelompok_tani" value="{{ old('nama_kelompok_tani') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                    @error('nama_kelompok_tani') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">NIK *</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Alamat *</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Rincian permohonan --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-fuel-pump"></i>
                <span>Rincian Permohonan</span>
            </p>

            <div class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Luas Lahan (hektar) *</label>
                        <input type="number" step="0.01" name="luas_lahan" value="{{ old('luas_lahan') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('luas_lahan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Jumlah Liter Diajukan *</label>
                        <input type="number" name="jumlah_liter_diajukan" value="{{ old('jumlah_liter_diajukan') }}" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                        @error('jumlah_liter_diajukan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Keperluan *</label>
                    <textarea name="keperluan" rows="3" placeholder="Jelaskan keperluan penggunaan solar" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">{{ old('keperluan') }}</textarea>
                    @error('keperluan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Berkas pendukung --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-paperclip"></i>
                <span>Berkas Pendukung</span>
            </p>

            <label class="flex items-center gap-3 border border-dashed border-[#E1DCC9] rounded-xl px-4 py-4 cursor-pointer hover:border-[#2D6A4F] transition-colors">
                <i class="bi bi-cloud-arrow-up text-xl text-[#5C6B62]"></i>
                <span class="text-sm text-[#5C6B62]">Klik untuk unggah KTP/foto lahan <span class="text-[#5C6B62]">(opsional, PDF/JPG/PNG, maks 2MB)</span></span>
                <input type="file" name="file_pendukung" class="hidden" onchange="this.previousElementSibling.textContent = this.files[0]?.name ?? this.previousElementSibling.textContent">
            </label>
            @error('file_pendukung') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('user.surat-solar.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">
                <i class="bi bi-send"></i>
                <span>Kirim Pengajuan</span>
            </button>
        </div>
    </form>

@endsection