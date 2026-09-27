@extends('layouts.public')

@section('title', 'Galeri Video')

@section('content')

    <h1 class="font-serif text-3xl text-[#1B4332] mb-1">Galeri Video</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Dokumentasi kegiatan UPTD Pertanian Warungkondang.</p>

    @if ($data->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-play-circle text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada video.</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-5">
            @foreach ($data as $item)
                @php
                    $embedUrl = null;
                    if ($item->video_url && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $item->video_url, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/'.$m[1];
                    }
                @endphp
                <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
                    <div class="aspect-video bg-[#F5F3EC]">
                        @if ($embedUrl)
                            <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
                        @elseif ($item->video_url)
                            <a href="{{ $item->video_url }}" target="_blank" class="flex items-center justify-center w-full h-full text-[#5C6B62]">
                                <i class="bi bi-play-circle text-3xl"></i>
                            </a>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="font-medium text-[#14231C]">{{ $item->judul }}</p>
                        @if ($item->tanggal)
                            <p class="text-xs text-[#5C6B62] mt-1">{{ $item->tanggal->translatedFormat('d M Y') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection