@extends('layouts.user')

@section('title', 'Detail Pengajuan')

@section('content')
    <a href="{{ route('user.surat-solar.index') }}" class="text-sm text-[#5C6B62] mb-4 inline-block">← Kembali</a>

    <div class="bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#1B4332]">Detail Pengajuan</h2>
            <span @class([
                'text-xs px-3 py-1 rounded-full font-medium',
                'bg-yellow-100 text-yellow-800' => $suratSolar->status === 'pending',
                'bg-blue-100 text-blue-800' => $suratSolar->status === 'diproses',
                'bg-green-100 text-green-800' => $suratSolar->status === 'diterima',
                'bg-red-100 text-red-800' => $suratSolar->status === 'ditolak',
            ])>
                {{ ucfirst($suratSolar->status) }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-[#5C6B62]">Nama Kelompok Tani</dt><dd class="font-medium">{{ $suratSolar->nama_kelompok_tani ?: '-' }}</dd></div>
            <div><dt class="text-[#5C6B62]">NIK</dt><dd class="font-medium">{{ $suratSolar->nik }}</dd></div>
            <div><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $suratSolar->alamat }}</dd></div>
            <div><dt class="text-[#5C6B62]">Luas Lahan</dt><dd class="font-medium">{{ $suratSolar->luas_lahan }} ha</dd></div>
            <div><dt class="text-[#5C6B62]">Jumlah Diajukan</dt><dd class="font-medium">{{ $suratSolar->jumlah_liter_diajukan }} Liter</dd></div>
            <div><dt class="text-[#5C6B62]">Tanggal Pengajuan</dt><dd class="font-medium">{{ $suratSolar->created_at->translatedFormat('d M Y, H:i') }}</dd></div>
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

        @if ($suratSolar->catatan_admin)
            <div class="bg-[#F5F3EC] rounded-lg p-4">
                <dt class="text-sm text-[#5C6B62] mb-1">Catatan Admin</dt>
                <dd class="text-sm">{{ $suratSolar->catatan_admin }}</dd>
            </div>
        @endif
    </div>
@endsection