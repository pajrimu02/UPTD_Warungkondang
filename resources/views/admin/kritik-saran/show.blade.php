@extends('layouts.admin')

@section('title', 'Detail Kritik & Saran')

@section('content')

    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
        <a href="{{ route('admin.kritiksaran.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] hover:text-[#1B4332]">
            <i class="bi bi-arrow-left"></i><span>Kembali ke Daftar</span>
        </a>

        <a href="{{ route('admin.kritiksaran.kelola', $kritikSaran) }}"
           class="inline-flex items-center gap-1.5 bg-[#1B4332] text-white text-xs font-medium px-4 py-2 rounded-full">
            <i class="bi bi-sliders"></i> Kelola & Tanggapi
        </a>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-6">
        <div class="flex items-start justify-between gap-4 flex-wrap pb-4 border-b border-[#E1DCC9]">
            <div>
                <p class="text-xs font-semibold tracking-wider text-[#2D6A4F] uppercase">{{ $kritikSaran->kategori }}</p>
                <h1 class="text-xl font-semibold text-[#1B4332] mt-1">{{ $kritikSaran->user?->name ?? 'Pengguna' }}</h1>
                <p class="text-xs text-[#5C6B62] mt-0.5">
                    {{ $kritikSaran->user?->email ?? '-' }} · Dikirim {{ $kritikSaran->created_at->translatedFormat('d F Y, H:i') }}
                </p>
            </div>

            <span @class([
                'text-xs px-3.5 py-1.5 rounded-full font-semibold',
                'bg-amber-100 text-amber-800' => $kritikSaran->status === 'baru',
                'bg-emerald-100 text-emerald-800' => $kritikSaran->status === 'ditanggapi',
            ])>
                {{ $kritikSaran->status === 'ditanggapi' ? 'Ditanggapi' : 'Baru' }}
            </span>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#8C9086] mb-2">Isi Kritik & Saran Petani</p>
            <div class="rounded-2xl bg-[#FAF9F4] border border-[#E1DCC9] p-4 text-sm text-[#14231C] leading-relaxed whitespace-pre-line">{{ $kritikSaran->isi }}</div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#8C9086] mb-2">Tanggapan Resmi UPTD</p>
            @if ($kritikSaran->tanggapan_admin)
                <div class="rounded-2xl bg-[#F4F8F5] border-l-4 border-[#2D6A4F] p-4 text-sm text-[#14231C] leading-relaxed whitespace-pre-line">{{ $kritikSaran->tanggapan_admin }}</div>
                <p class="text-xs text-[#8C9086] mt-2">Terakhir diperbarui {{ $kritikSaran->updated_at->translatedFormat('d F Y, H:i') }}</p>
            @else
                <p class="text-sm text-[#5C6B62] italic">Belum ada tanggapan dari petugas UPTD.</p>
            @endif
        </div>
    </div>

@endsection