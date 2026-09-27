@extends('layouts.user')

@section('title', 'Kritik & Saran')

@section('content')
    <h2 class="font-semibold text-xl text-[#1B4332] mb-6">Kritik & Saran</h2>

    <form method="POST" action="{{ route('user.kritiksaran.store') }}" class="bg-white border border-[#E1DCC9] rounded-xl p-6 space-y-5 mb-8">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select name="kategori" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
                <option value="">— Pilih kategori —</option>
                <option value="keluhan" {{ old('kategori') === 'keluhan' ? 'selected' : '' }}>Keluhan</option>
                <option value="saran" {{ old('kategori') === 'saran' ? 'selected' : '' }}>Saran</option>
                <option value="pertanyaan" {{ old('kategori') === 'pertanyaan' ? 'selected' : '' }}>Pertanyaan</option>
            </select>
            @error('kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Isi Pesan</label>
            <textarea name="isi" rows="4" placeholder="Tulis kritik, saran, atau pertanyaan kamu di sini..." class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">{{ old('isi') }}</textarea>
            @error('isi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2 rounded-full">Kirim</button>
        </div>
    </form>

    <h3 class="font-semibold text-[#1B4332] mb-3">Riwayat Kiriman Kamu</h3>

    @if ($riwayat->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-6 text-center text-sm text-[#5C6B62]">
            Belum ada kritik/saran yang kamu kirim.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($riwayat as $item)
                <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-[#1B4332] uppercase">{{ $item->kategori }}</span>
                        <span @class([
                            'text-xs px-3 py-1 rounded-full font-medium',
                            'bg-yellow-100 text-yellow-800' => $item->status === 'pending',
                            'bg-blue-100 text-blue-800' => $item->status === 'diproses',
                            'bg-green-100 text-green-800' => $item->status === 'selesai',
                        ])>
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-[#14231C]">{{ $item->isi }}</p>
                    <p class="text-xs text-[#5C6B62] mt-2">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</p>

                    @if ($item->tanggapan_admin)
                        <div class="bg-[#F5F3EC] rounded-lg p-3 mt-3">
                            <p class="text-xs text-[#5C6B62] mb-1">Tanggapan Admin:</p>
                            <p class="text-sm">{{ $item->tanggapan_admin }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection