@extends('layouts.admin')

@section('title', 'Kelola Kritik & Saran')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332]">Kelola Kritik & Saran</h2>

        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
            <option value="">Semua Status</option>
            <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
            <option value="ditanggapi" {{ request('status') === 'ditanggapi' ? 'selected' : '' }}>Ditanggapi</option>
        </select>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($kritikSarans as $item)
            <a href="{{ route('admin.kritiksaran.show', $item) }}" class="block bg-white border border-[#E1DCC9] rounded-xl p-4 hover:border-[#2D6A4F] transition">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <span class="text-xs font-medium text-[#1B4332] uppercase">{{ $item->kategori }}</span>
                        <span class="text-xs text-[#5C6B62]"> · {{ $item->user->name }} · {{ $item->created_at->diffForHumans() }}</span>
                    </div>
                    <span @class([
                    'text-xs px-3 py-1 rounded-full font-medium',
                    'bg-amber-50 text-amber-700 border border-amber-200' => $item->status === 'baru',
                    'bg-emerald-50 text-emerald-700 border border-emerald-200' => $item->status === 'ditanggapi',
                ])>{{ $item->status === 'ditanggapi' ? 'Ditanggapi' : 'Baru' }}</span>
                </div>
                <p class="text-sm text-[#14231C]">{{ Str::limit($item->isi, 120) }}</p>
            </a>
        @empty
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-6 text-center text-sm text-[#5C6B62]">
                Belum ada kritik/saran masuk.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $kritikSarans->links() }}</div>
@endsection