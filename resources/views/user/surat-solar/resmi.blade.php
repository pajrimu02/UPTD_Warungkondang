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

        @if ($data->status === 'diterima' && $data->file_surat_resmi)
            <div class="flex items-center justify-between bg-[#EFF6F0] border border-[#2D6A4F]/30 rounded-xl p-4">
                <div>
                    <p class="text-sm font-medium text-[#1B4332]">Nomor Surat: {{ $data->nomor_surat ?? '-' }}</p>
                    <p class="text-xs text-[#5C6B62] mt-1">
                        Disetujui {{ $data->disetujui_at?->format('d M Y') ?? '-' }}
                        @if ($data->disetujuiOleh) oleh {{ $data->disetujuiOleh->name }} @endif
                    </p>
                </div>
                <a href="{{ asset('storage/' . $data->file_surat_resmi) }}" target="_blank" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full flex items-center gap-2 whitespace-nowrap">
                    <i class="bi bi-download"></i> Unduh PDF
                </a>
            </div>
        @elseif ($data->status === 'ditolak')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-sm font-medium text-red-800">Pengajuan ditolak</p>
                <p class="text-sm text-red-700 mt-1">{{ $data->catatan_admin ?? 'Tidak ada catatan dari admin.' }}</p>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-sm text-amber-800">Surat resmi belum diterbitkan. Status saat ini: <b>{{ ucfirst($data->status) }}</b>.</p>
            </div>
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