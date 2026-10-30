@extends('layouts.admin')

@section('title', 'Detail Pengajuan Solar')

@section('content')

    @php
        $spkColor = match ($suratSolar->spk_label) {
            'layak' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'perlu_ditinjau' => 'bg-amber-50 text-amber-700 border-amber-200',
            'tidak_layak' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]',
        };
        $spkLabelText = match ($suratSolar->spk_label) {
            'layak' => 'Layak Diproses',
            'perlu_ditinjau' => 'Perlu Ditinjau Admin',
            'tidak_layak' => 'Tidak Layak (Skor Rendah)',
            default => 'Belum dinilai',
        };
    @endphp

    <a href="{{ route('admin.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-[#1B4332]">Detail Pengajuan</h1>
            @include('admin._badge', ['status' => $suratSolar->status])
        </div>
        <a href="{{ route('admin.surat-solar.kelola', $suratSolar) }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
            <i class="bi bi-sliders"></i><span>Kelola Pengajuan</span>
        </a>
    </div>

    @if ($suratSolar->spk_skor !== null)
        <div class="flex items-center gap-3 rounded-2xl border px-5 py-4 mb-6 {{ $spkColor }}">
            <i class="bi bi-cpu text-xl"></i>
            <div>
                <p class="font-medium">Rekomendasi SPK: {{ $spkLabelText }}</p>
                <p class="text-sm opacity-80">Skor kelayakan otomatis: {{ $suratSolar->spk_skor }}/100 (berdasarkan kelengkapan dokumen & riwayat pengajuan sebelumnya). Keputusan akhir tetap di tangan admin.</p>
            </div>
        </div>
    @endif

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">

        <div>
            <p class="text-xs text-[#5C6B62] mb-2">Data Pemohon</p>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-[#5C6B62]">Nama</dt><dd class="font-medium">{{ $suratSolar->nama_pemohon ?? $suratSolar->user->name }}</dd></div>
                <div><dt class="text-[#5C6B62]">NIK</dt><dd class="font-medium">{{ $suratSolar->nik }}</dd></div>
                <div><dt class="text-[#5C6B62]">No. HP</dt><dd class="font-medium">{{ $suratSolar->no_hp }}</dd></div>
                <div><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $suratSolar->alamat }}</dd></div>
                <div><dt class="text-[#5C6B62]">Poktan</dt><dd class="font-medium">{{ $suratSolar->poktan?->nama_kelompok ?? 'Mandiri (tanpa poktan)' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Jenis Usaha</dt><dd class="font-medium">{{ $suratSolar->jenis_usaha }}</dd></div>
                @if ($suratSolar->nama_kapal)
                    <div><dt class="text-[#5C6B62]">Nama Kapal</dt><dd class="font-medium">{{ $suratSolar->nama_kapal }}</dd></div>
                @endif
            </dl>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-xs text-[#5C6B62] mb-2">Data Alat/Mesin</p>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-[#5C6B62]">Jenis Alat/Mesin</dt><dd class="font-medium">{{ $suratSolar->jenis_alat_mesin }}</dd></div>
                <div><dt class="text-[#5C6B62]">Fungsi</dt><dd class="font-medium">{{ $suratSolar->fungsi_alat_mesin }}</dd></div>
                <div><dt class="text-[#5C6B62]">Jumlah</dt><dd class="font-medium">{{ $suratSolar->jumlah_alat_mesin }}</dd></div>
                <div><dt class="text-[#5C6B62]">Daya</dt><dd class="font-medium">{{ $suratSolar->daya_alat_mesin ?: '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Lama Penggunaan</dt><dd class="font-medium">{{ $suratSolar->lama_penggunaan ?: '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Lama Operasi</dt><dd class="font-medium">{{ $suratSolar->lama_operasi ?: '—' }}</dd></div>
            </dl>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-xs text-[#5C6B62] mb-2">Usulan Volume</p>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-[#5C6B62]">Volume Diajukan</dt><dd class="font-medium">{{ $suratSolar->usulan_volume_konsumsi }} L / {{ $suratSolar->volume_periode }}</dd></div>
                <div><dt class="text-[#5C6B62]">Estimasi Sisa</dt><dd class="font-medium">{{ $suratSolar->estimasi_sisa_liter ?? '—' }} L</dd></div>
            </dl>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-xs text-[#5C6B62] mb-2">Berkas Pendukung</p>
            <div class="flex flex-wrap gap-3">
                @foreach (['file_ktp' => 'KTP', 'file_sku' => 'SKU', 'file_foto_mesin' => 'Foto Mesin'] as $field => $label)
                    @if ($suratSolar->$field)
                        <a href="{{ asset('storage/'.$suratSolar->$field) }}" target="_blank" class="flex items-center gap-1.5 text-sm text-[#1B4332] underline">
                            <i class="bi bi-paperclip"></i>{{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        @if ($suratSolar->nomor_surat || $suratSolar->catatan_admin)
            <hr class="border-[#E1DCC9]">
            <div>
                <p class="text-xs text-[#5C6B62] mb-2">Keputusan Admin</p>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    @if ($suratSolar->nomor_surat)
                        <div><dt class="text-[#5C6B62]">Nomor Surat</dt><dd class="font-medium">{{ $suratSolar->nomor_surat }}</dd></div>
                    @endif
                    @if ($suratSolar->catatan_admin)
                        <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Catatan</dt><dd class="font-medium">{{ $suratSolar->catatan_admin }}</dd></div>
                    @endif
                </dl>
            </div>
        @endif

        <p class="text-xs text-[#8C9086]">Diajukan {{ $suratSolar->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
    </div>

@endsection