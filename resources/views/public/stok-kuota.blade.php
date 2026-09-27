@extends('layouts.public')

@section('title', 'Stok & Kuota')

@section('content')

    <h1 class="font-serif text-3xl text-[#1B4332] mb-1">Stok & Kuota Pupuk / Solar</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Informasi ketersediaan yang diperbarui langsung oleh UPTD.</p>

    @if ($data->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-info-circle text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada informasi yang tersedia.</p>
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
            @foreach ($data as $item)
                <div class="flex items-start gap-4 px-5 py-4">
                    <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                        <i class="bi {{ $item->jenis === 'solar' ? 'bi-fuel-pump' : 'bi-flower1' }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-medium text-[#14231C]">{{ $item->judul }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-[#F5F3EC] text-[#5C6B62] capitalize">{{ $item->jenis }}</span>
                        </div>
                        <p class="text-sm text-[#5C6B62]">{{ $item->isi }}</p>
                        @if ($item->periode)
                            <p class="text-xs text-[#5C6B62] mt-2"><i class="bi bi-calendar3"></i> {{ $item->periode }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection