@extends('layouts.admin')

@section('title', 'Kelola & Tanggapi Kritik Saran')

@section('content')

    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
        <a href="{{ route('admin.kritiksaran.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] hover:text-[#1B4332]">
            <i class="bi bi-arrow-left"></i><span>Kembali ke Daftar</span>
        </a>

        <a href="{{ route('admin.kritiksaran.show', $kritikSaran) }}"
           class="inline-flex items-center gap-1.5 border border-[#E1DCC9] bg-white text-[#1B4332] text-xs font-medium px-4 py-2 rounded-full hover:border-[#2D6A4F]">
            <i class="bi bi-eye"></i> Lihat Detail
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Pesan Petani --}}
        <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between gap-3 flex-wrap pb-4 border-b border-[#E1DCC9]">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#2D6A4F]">{{ $kritikSaran->kategori }}</span>
                    <h1 class="text-xl font-semibold text-[#1B4332] mt-0.5">{{ $kritikSaran->user?->name ?? 'Pengguna' }}</h1>
                    <p class="text-xs text-[#5C6B62]">{{ $kritikSaran->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>

                <span @class([
                    'text-xs px-3 py-1 rounded-full font-semibold',
                    'bg-amber-100 text-amber-800' => $kritikSaran->status === 'baru',
                    'bg-emerald-100 text-emerald-800' => $kritikSaran->status === 'ditanggapi',
                ])>
                    Status Saat Ini: {{ $kritikSaran->status === 'ditanggapi' ? 'Ditanggapi' : 'Baru' }}
                </span>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#8C9086] mb-2">Pesan Masuk</p>
                <div class="rounded-2xl bg-[#FAF9F4] border border-[#E1DCC9] p-4 text-sm text-[#14231C] leading-relaxed whitespace-pre-line">{{ $kritikSaran->isi }}</div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Tanggapan Admin --}}
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 h-fit">
            <h2 class="font-semibold text-[#1B4332] mb-1">Tanggapan UPTD</h2>
            <p class="text-xs text-[#5C6B62] mb-4">
                Saat tanggapan dikirim, status otomatis berubah menjadi <b>Ditanggapi</b> dan tampil di halaman publik.
            </p>

            <form method="POST" action="{{ route('admin.kritiksaran.update', $kritikSaran) }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-[#14231C] mb-1.5">Isi Tanggapan Resmi *</label>
                    <textarea name="tanggapan_admin" rows="6" required
                              placeholder="Tuliskan penjelasan atau tindak lanjut dari UPTD..."
                              class="w-full rounded-xl border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">{{ old('tanggapan_admin', $kritikSaran->tanggapan_admin) }}</textarea>
                    @error('tanggapan_admin')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-[#1B4332] hover:bg-[#2D6A4F] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition">
                    <i class="bi bi-send"></i> {{ $kritikSaran->status === 'ditanggapi' ? 'Perbarui Tanggapan' : 'Kirim Tanggapan' }}
                </button>
            </form>
        </div>
    </div>

@endsection