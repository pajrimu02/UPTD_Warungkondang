@extends('layouts.public')

@section('title', 'Galeri Foto')

@section('content')

    <h1 class="font-serif text-3xl text-[#1B4332] mb-1">Galeri Foto</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Dokumentasi kegiatan UPTD Pertanian Warungkondang.</p>

    @if ($data->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-images text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada foto.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($data as $item)
                <a href="{{ $item->file_path ? asset('storage/'.$item->file_path) : '#' }}" target="_blank" class="block bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden hover:border-[#2D6A4F] transition-colors">
                    <div class="aspect-square bg-[#F5F3EC]">
                        @if ($item->file_path)
                            <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-sm font-medium text-[#14231C] truncate">{{ $item->judul }}</p>
                        @if ($item->tanggal)
                            <p class="text-xs text-[#5C6B62] mt-0.5">{{ $item->tanggal->translatedFormat('d M Y') }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection