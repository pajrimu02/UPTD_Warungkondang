@extends('layouts.user')

@section('title', 'Surat Rekomendasi Pupuk')

@section('content')

    <a href="{{ route('user.surat-pengajuan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Surat Rekomendasi Pupuk</h1>
            <p class="text-sm text-[#5C6B62]">Riwayat pengajuan poktan kamu.</p>
        </div>
        <a href="{{ route('user.surat-pupuk.create') }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white px-4 py-2.5 rounded-full text-sm font-medium whitespace-nowrap">
            <i class="bi bi-plus-lg"></i> Ajukan Baru
        </a>
    </div>

    <div class="space-y-3">
        @forelse ($suratPupuks as $item)
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-medium text-[#14231C]">{{ $item->items_count }} petani</p>
                    @include('user.surat-pengajuan._badge', ['status' => $item->status])
                </div>
                <p class="text-sm text-[#5C6B62] mt-1">Diajukan {{ $item->created_at->format('d M Y') }}</p>

                <div class="flex items-center gap-2 mt-3">
                    <a href="{{ route('user.surat-pupuk.show', $item) }}" class="flex-1 text-center text-sm border border-[#E1DCC9] text-[#1B4332] rounded-lg py-2 hover:border-[#2D6A4F] transition">
                        <i class="bi bi-file-earmark-text"></i> Surat Permohonan
                    </a>
                    <a href="{{ route('user.surat-pupuk.resmi', $item) }}" class="flex-1 text-center text-sm border border-[#E1DCC9] text-[#1B4332] rounded-lg py-2 hover:border-[#2D6A4F] transition">
                        <i class="bi bi-patch-check"></i> Surat Resmi
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada pengajuan.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $suratPupuks->links() }}</div>

@endsection