@extends('layouts.admin')

@section('title', 'Kelola Stok & Kuota')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Stok Solar & Pupuk</h1>
            <p class="text-sm text-[#5C6B62] mt-1">Informasi yang ditampilkan di halaman publik.</p>
        </div>
        <a href="{{ route('admin.stok-kuota.create') }}" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full shrink-0">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah</span>
        </a>
    </div>

    <form method="GET" class="mb-4">
        <select name="jenis" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
            <option value="">Semua Jenis</option>
            <option value="solar" {{ request('jenis') === 'solar' ? 'selected' : '' }}>Solar</option>
            <option value="pupuk" {{ request('jenis') === 'pupuk' ? 'selected' : '' }}>Pupuk</option>
        </select>
    </form>

    @if ($stokKuotas->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-fuel-pump text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada informasi stok/kuota.</p>
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
            @foreach ($stokKuotas as $item)
                <div class="flex items-center gap-4 px-5 py-4">
                    <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                        <i class="bi {{ $item->jenis === 'solar' ? 'bi-fuel-pump' : 'bi-flower1' }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-[#14231C]">{{ $item->judul }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-[#F5F3EC] text-[#5C6B62] capitalize">{{ $item->jenis }}</span>
                        </div>
                        <p class="text-sm text-[#5C6B62] mt-0.5">{{ Str::limit($item->isi, 80) }}</p>
                        @if ($item->periode)
                            <p class="text-xs text-[#5C6B62] mt-1"><i class="bi bi-calendar3"></i> {{ $item->periode }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.stok-kuota.edit', $item) }}" class="text-sm text-[#1B4332] underline">Edit</a>
                        <form method="POST" action="{{ route('admin.stok-kuota.destroy', $item) }}" onsubmit="return confirm('Hapus informasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $stokKuotas->links() }}</div>
    @endif

@endsection