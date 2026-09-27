@extends('layouts.admin')

@section('title', 'Detail Kritik & Saran')

@section('content')
    <a href="{{ route('admin.kritiksaran.index') }}" class="text-sm text-[#5C6B62] mb-4 inline-block">← Kembali</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-[#1B4332]">{{ ucfirst($kritikSaran->kategori) }}</h2>
                <span class="text-xs text-[#5C6B62]">{{ $kritikSaran->created_at->translatedFormat('d M Y, H:i') }}</span>
            </div>

            <p class="text-sm text-[#5C6B62]">Dari: <span class="font-medium text-[#14231C]">{{ $kritikSaran->user->name }}</span> ({{ $kritikSaran->user->email }})</p>

            <div class="bg-[#F5F3EC] rounded-lg p-4">
                <p class="text-sm">{{ $kritikSaran->isi }}</p>
            </div>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-xl p-6">
            <h3 class="font-semibold text-[#1B4332] mb-4">Tanggapi</h3>

            <form method="POST" action="{{ route('admin.kritiksaran.update', $kritikSaran) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-[#E1DCC9] text-sm">
                    <option value="baru" {{ $kritikSaran->status === 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="ditanggapi" {{ $kritikSaran->status === 'ditanggapi' ? 'selected' : '' }}>Ditanggapi</option>
                </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggapan Admin</label>
                    <textarea name="tanggapan_admin" rows="5" class="w-full rounded-lg border-[#E1DCC9] text-sm">{{ old('tanggapan_admin', $kritikSaran->tanggapan_admin) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Simpan</button>
            </form>
        </div>
    </div>
@endsection