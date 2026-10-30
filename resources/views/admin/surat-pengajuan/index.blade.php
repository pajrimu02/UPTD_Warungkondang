@extends('layouts.admin')

@section('title', 'Pengelolaan Surat')

@section('content')

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Pengelolaan Surat Rekomendasi</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Pilih jenis surat yang ingin dikelola.</p>

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ($cards as $c)
            <a href="{{ $c['url'] }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:border-[#2D6A4F] transition flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#2D6A4F] flex items-center justify-center text-2xl">
                        <i class="bi {{ $c['ikon'] }}"></i>
                    </span>
                    @if ($c['menunggu'] > 0)
                        <span class="text-xs px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            {{ $c['menunggu'] }} menunggu
                        </span>
                    @endif
                </div>

                <p class="font-semibold text-[#1B4332] text-lg">{{ $c['judul'] }}</p>
                <p class="text-sm text-[#5C6B62] mt-1 flex-1">{{ $c['deskripsi'] }}</p>

                <div class="flex items-center justify-between mt-4 text-xs text-[#5C6B62]">
                    <span>{{ $c['total'] }} pengajuan</span>
                    <span>{{ $c['terbaru'] ? 'Terbaru '.\Illuminate\Support\Carbon::parse($c['terbaru'])->diffForHumans() : 'Belum ada' }}</span>
                </div>
            </a>
        @endforeach
    </div>

@endsection