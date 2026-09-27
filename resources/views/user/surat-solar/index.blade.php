@extends('layouts.user')

@section('title', 'Surat Rekomendasi Solar')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332]">Surat Rekomendasi Solar</h2>
        <a href="{{ route('user.surat-solar.create') }}" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">
            + Ajukan Baru
        </a>
    </div>

    @if ($suratSolars->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-8 text-center text-sm text-[#5C6B62]">
            Belum ada pengajuan. Klik "Ajukan Baru" untuk mulai.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($suratSolars as $surat)
                <a href="{{ route('user.surat-solar.show', $surat) }}" class="block bg-white border border-[#E1DCC9] rounded-xl p-4 hover:border-[#2D6A4F] transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-[#14231C]">{{ $surat->jumlah_liter_diajukan }} Liter</p>
                            <p class="text-xs text-[#5C6B62] mt-1">Diajukan {{ $surat->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                        <span @class([
                            'text-xs px-3 py-1 rounded-full font-medium',
                            'bg-yellow-100 text-yellow-800' => $surat->status === 'pending',
                            'bg-blue-100 text-blue-800' => $surat->status === 'diproses',
                            'bg-green-100 text-green-800' => $surat->status === 'diterima',
                            'bg-red-100 text-red-800' => $surat->status === 'ditolak',
                        ])>
                            {{ ucfirst($surat->status) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $suratSolars->links() }}
        </div>
    @endif
@endsection