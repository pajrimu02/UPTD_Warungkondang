@extends('layouts.ketua')

@section('title', 'Surat Menunggu Tanda Tangan')

@section('content')

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Surat Menunggu Tanda Tangan</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Cetak surat, tanda tangani, pindai, lalu unggah scan di sini. Scan otomatis terkirim ke admin.</p>

    @error('file_surat_resmi')
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
    @enderror

    <div class="space-y-4 mb-8">
        @forelse ($menunggu as $s)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $s->nama_pemohon ?? $s->user->name }}</p>
                        <p class="text-sm text-[#5C6B62]">{{ $s->nomor_surat }} · {{ number_format($s->alokasi_volume, 0, ',', '.') }} L/minggu</p>
                        <p class="text-xs text-[#8C9086]">Dikirim {{ $s->ttd_dikirim_at->translatedFormat('d M Y, H:i') }} · {{ $s->poktan?->nama_kelompok ?? 'Mandiri' }}</p>
                    </div>
                    <a href="{{ route('ketua.surat.cetak-solar', $s) }}" target="_blank" class="inline-flex items-center gap-2 border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full">
                        <i class="bi bi-printer"></i> Lihat / Cetak Surat
                    </a>
                </div>

                <form method="POST" action="{{ route('ketua.surat.upload-solar', $s) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 border-t border-[#E1DCC9] mt-4 pt-4">
                    @csrf
                    <input type="file" name="file_surat_resmi" required accept=".pdf,.jpg,.jpeg,.png,image/*" class="text-sm flex-1 min-w-[200px]">
                    <button type="submit" class="bg-[#1B4332] text-white text-sm px-5 py-2 rounded-full shrink-0">
                        <i class="bi bi-cloud-arrow-up"></i> Unggah Scan & Kirim ke Admin
                    </button>
                </form>
            </div>
        @empty
            <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-12 text-center text-sm text-[#5C6B62]">Tidak ada surat yang menunggu.</div>
        @endforelse
    </div>

    <h2 class="font-semibold text-[#1B4332] mb-3">Terakhir Ditandatangani</h2>
    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($selesai as $s)
            <div class="flex items-center justify-between px-5 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $s->nama_pemohon ?? $s->user->name }}</p>
                    <p class="text-xs text-[#5C6B62]">{{ $s->nomor_surat }}</p>
                </div>
                <span class="text-xs text-[#5C6B62]">{{ $s->ttd_kembali_at->translatedFormat('d M Y') }}</span>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-sm text-[#5C6B62]">Belum ada.</div>
        @endforelse
    </div>

@endsection