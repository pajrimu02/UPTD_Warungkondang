@extends('layouts.user')

@section('title', 'Surat Resmi')

@section('content')

    <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Surat Resmi</h1>
        <span class="text-xs px-3 py-1.5 rounded-full
            @class([
                'bg-amber-100 text-amber-800' => $data->status === 'diajukan',
                'bg-sky-100 text-sky-800' => $data->status === 'diproses',
                'bg-emerald-100 text-emerald-800' => $data->status === 'diterima',
                'bg-red-100 text-red-800' => $data->status === 'ditolak',
            ])">
            {{ ucfirst($data->status) }}
        </span>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">

        @if ($data->status === 'diterima' && $data->file_surat_resmi && $data->diserahkan_at)
            {{-- blok unduh yang sudah ada, tidak diubah --}}
        @elseif ($data->status === 'diproses' && $data->nomor_surat)
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                <p class="text-sm font-medium text-sky-800">Pengajuan disetujui. Surat sedang diproses.</p>
                <p class="text-sm text-sky-700 mt-1">
                    @if ($data->ttd_kembali_at) Surat sudah bertanda tangan dan akan segera dikirim ke kamu.
                    @elseif ($data->ttd_dikirim_at) Surat sedang menunggu tanda tangan ketua.
                    @else Admin sedang menyiapkan surat untuk ditandatangani. @endif
                </p>
                <p class="text-xs text-sky-700 mt-1">Nomor surat: {{ $data->nomor_surat }}</p>
            </div>
        @elseif ($data->status === 'ditolak')
            {{-- blok ditolak yang sudah ada --}}
        @else
            {{-- blok "belum diterbitkan" yang sudah ada --}}
        @endif

        @if ($data->catatan_admin && $data->status !== 'ditolak')
            <div>
                <p class="text-sm font-medium text-[#1B4332] mb-1">Catatan Admin</p>
                <p class="text-sm text-[#5C6B62]">{{ $data->catatan_admin }}</p>
            </div>
        @endif

        @if ($data->spk_label)
            <div class="text-xs text-[#8C9086] border-t border-[#E1DCC9] pt-4">
                Skor kelayakan sistem: {{ $data->spk_skor }}/100 ({{ $data->spk_label === 'layak' ? 'Layak diproses' : 'Perlu verifikasi manual' }}) — ini rekomendasi otomatis, keputusan akhir tetap dari admin.
            </div>
        @endif

    </div>

@endsection