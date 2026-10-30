@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Pupuk')

@section('content')
<a href="{{ route('admin.surat-pengajuan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
    <i class="bi bi-arrow-left"></i><span>Kembali</span>
</a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Pengajuan Surat Pupuk</h1>
        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua Status</option>
                @foreach (['diajukan', 'diproses', 'diterima', 'ditolak'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($suratPupuks as $surat)
            <a href="{{ route('admin.surat-pupuk.show', $surat) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
                <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                    <i class="bi bi-flower2"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-[#14231C]">{{ $surat->poktan->nama_kelompok }}</p>
                    <p class="text-sm text-[#5C6B62]">{{ $surat->items_count }} petani · oleh {{ $surat->user->name }} · {{ $surat->created_at->translatedFormat('d M Y') }}</p>
                </div>
                @include('admin._badge', ['status' => $surat->status])
                <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
            </a>
        @empty
            <div class="px-6 py-14 text-center"><p class="text-sm text-[#5C6B62]">Belum ada pengajuan.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $suratPupuks->links() }}</div>

@endsection