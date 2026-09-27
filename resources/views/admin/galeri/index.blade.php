@extends('layouts.admin')

@section('title', 'Kelola Galeri')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Galeri Foto & Video</h1>
            <p class="text-sm text-[#5C6B62] mt-1">Konten yang ditampilkan di halaman Galeri publik.</p>
        </div>
        <a href="{{ route('admin.galeri.create') }}" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full shrink-0">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah</span>
        </a>
    </div>

    <form method="GET" class="mb-4">
        <select name="jenis" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
            <option value="">Semua Jenis</option>
            <option value="foto" {{ request('jenis') === 'foto' ? 'selected' : '' }}>Foto</option>
            <option value="video" {{ request('jenis') === 'video' ? 'selected' : '' }}>Video</option>
        </select>
    </form>

    @if ($galeris->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-images text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada item galeri.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($galeris as $item)
                <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
                    <div class="aspect-video bg-[#F5F3EC] flex items-center justify-center">
                        @if ($item->jenis === 'foto' && $item->file_path)
                            <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-full object-cover">
                        @else
                            <i class="bi bi-play-circle text-3xl text-[#5C6B62]"></i>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-sm font-medium text-[#14231C] truncate">{{ $item->judul }}</p>
                        <p class="text-xs text-[#5C6B62] mt-0.5 capitalize">{{ $item->jenis }} @if($item->tanggal) · {{ $item->tanggal->translatedFormat('d M Y') }} @endif</p>
                        <div class="flex items-center gap-3 mt-2">
                            <a href="{{ route('admin.galeri.edit', $item) }}" class="text-xs text-[#1B4332] underline">Edit</a>
                            <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" onsubmit="return confirm('Hapus item ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 underline">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $galeris->links() }}</div>
    @endif

@endsection