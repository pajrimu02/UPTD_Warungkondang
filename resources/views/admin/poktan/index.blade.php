@extends('layouts.admin')

@section('title', 'Kelompok Tani')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Pengajuan Kelompok Tani</h1>
        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua (kecuali draft)</option>
                @foreach (['diajukan', 'disetujui', 'ditolak', 'draft'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($poktans as $p)
            <a href="{{ route('admin.poktan.show', $p) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
                <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                    <i class="bi bi-diagram-3"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-[#14231C]">{{ $p->nama_kelompok }}</p>
                    <p class="text-sm text-[#5C6B62]">
                        Desa {{ $p->desa?->nama ?? '—' }} · {{ $p->anggota_count }} anggota
                        @if ($p->pengaju) · oleh {{ $p->pengaju->name }} @endif
                    </p>
                </div>
                @include('admin._badge', ['status' => $p->status])
                <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
            </a>
        @empty
            <div class="px-6 py-14 text-center"><p class="text-sm text-[#5C6B62]">Belum ada poktan.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $poktans->links() }}</div>

@endsection