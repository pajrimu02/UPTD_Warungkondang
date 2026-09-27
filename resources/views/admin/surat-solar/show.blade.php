@extends('layouts.admin')

@section('title', 'Detail Pengajuan Solar')

@section('content')
    <a href="{{ route('admin.surat-solar.index') }}" class="text-sm text-[#5C6B62] mb-4 inline-block">← Kembali</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-xl text-[#1B4332]">Detail Pengajuan</h2>

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-[#5C6B62]">Pemohon</dt><dd class="font-medium">{{ $suratSolar->user->name }}</dd></div>
                <div><dt class="text-[#5C6B62]">Email</dt><dd class="font-medium">{{ $suratSolar->user->email }}</dd></div>
                <div><dt class="text-[#5C6B62]">Nama Kelompok Tani</dt><dd class="font-medium">{{ $suratSolar->nama_kelompok_tani ?: '-' }}</dd></div>
                <div><dt class="text-[#5C6B62]">NIK</dt><dd class="font-medium">{{ $suratSolar->nik }}</dd></div>
                <div><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $suratSolar->alamat }}</dd></div>
                <div><dt class="text-[#5C6B62]">Luas Lahan</dt><dd class="font-medium">{{ $suratSolar->luas_lahan }} ha</dd></div>
                <div><dt class="text-[#5C6B62]">Jumlah Diajukan</dt><dd class="font-medium">{{ $suratSolar->jumlah_liter_diajukan }} Liter</dd></div>
                <div><dt class="text-[#5C6B62]">Tanggal</dt><dd class="font-medium">{{ $suratSolar->created_at->translatedFormat('d M Y, H:i') }}</dd></div>
            </dl>

            <div>
                <dt class="text-sm text-[#5C6B62] mb-1">Keperluan</dt>
                <dd class="text-sm">{{ $suratSolar->keperluan }}</dd>
            </div>

            @if ($suratSolar->file_pendukung)
                <div>
                    <dt class="text-sm text-[#5C6B62] mb-1">File Pendukung</dt>
                    <a href="{{ asset('storage/'.$suratSolar->file_pendukung) }}" target="_blank" class="text-sm text-[#1B4332] underline">Lihat file</a>
                </div>
            @endif
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-xl p-6">
            <h3 class="font-semibold text-[#1B4332] mb-4">Kelola Status</h3>

            <form method="POST" action="{{ route('admin.surat-solar.update', $suratSolar) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-[#E1DCC9] text-sm">
                        @foreach (['pending','diproses','diterima','ditolak'] as $s)
                            <option value="{{ $s }}" {{ $suratSolar->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $suratSolar->nomor_surat) }}" class="w-full rounded-lg border-[#E1DCC9] text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="4" class="w-full rounded-lg border-[#E1DCC9] text-sm">{{ old('catatan_admin', $suratSolar->catatan_admin) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Simpan</button>
            </form>
        </div>
    </div>
@endsection