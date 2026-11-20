@extends('layouts.ketua')

@section('title', $isKepala ? 'Tanda Tangan SK Pengukuhan Poktan' : 'Surat Menunggu Tanda Tangan')

@section('content')

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">
                {{ $isKepala ? 'Tanda Tangan SK Pengukuhan Poktan' : 'Tanda Tangan Surat Rekomendasi' }}
            </h1>
            <p class="text-sm text-[#5C6B62]">
                Cetak dokumen, tanda tangani, pindai menjadi PDF, lalu unggah di sini. Scan otomatis terkirim ke admin.
            </p>    
        </div>

        @unless ($isKepala)
            <form method="GET">
                <select name="jenis" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                    <option value="">Semua Jenis</option>
                    <option value="solar" @selected($jenis === 'solar')>BBM (Solar)</option>
                    <option value="pupuk" @selected($jenis === 'pupuk')>Pupuk</option>
                </select>
            </form>
        @endunless
    </div>

    @error('file_surat_resmi')
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
    @enderror

    {{-- Menunggu --}}
    <h2 class="font-semibold text-[#1B4332] mb-3">Menunggu Tanda Tangan <span class="text-sm font-normal text-[#5C6B62]">({{ $menunggu->count() }})</span></h2>
    <div class="space-y-4 mb-8">
        @forelse ($menunggu as $m)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0"><i class="bi {{ $m['ikon'] }}"></i></span>
                        <div>
                            <p class="font-medium">{{ $m['judul'] }} <span class="text-[11px] ml-1 px-2 py-0.5 rounded-full bg-[#F5F3EC] text-[#5C6B62]">{{ $m['jenis'] }}</span></p>
                            <p class="text-sm text-[#5C6B62]">{{ $m['nomor'] }} · {{ $m['info'] }}</p>
                            <p class="text-xs text-[#8C9086]">Dikirim {{ $m['waktu']->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <a href="{{ $m['cetak'] }}" target="_blank" class="inline-flex items-center gap-2 border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full">
                        <i class="bi bi-printer"></i> Lihat / Cetak {{ $isKepala ? 'SK' : 'Surat' }}
                    </a>
                </div>

                <form method="POST" action="{{ $m['upload'] }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 border-t border-[#E1DCC9] mt-4 pt-4">
                    @csrf
                    <input type="file" name="file_surat_resmi" required accept=".pdf,application/pdf" class="text-sm flex-1 min-w-[200px]">
                    <button type="submit" class="bg-[#1B4332] text-white text-sm px-5 py-2 rounded-full shrink-0">
                        <i class="bi bi-cloud-arrow-up"></i> Unggah PDF & Kirim ke Admin
                    </button>
                </form>
                <p class="text-xs text-[#8C9086] mt-2">Hanya PDF, maksimal 4 MB.</p>
            </div>
        @empty
            <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-12 text-center text-sm text-[#5C6B62]">Tidak ada dokumen yang menunggu.</div>
        @endforelse
    </div>

    {{-- Sudah ditandatangani --}}
    <h2 class="font-semibold text-[#1B4332] mb-3">Terakhir Ditandatangani</h2>
    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($selesai as $m)
            <div class="flex flex-wrap items-center gap-3 px-5 py-3.5 text-sm">
                <div class="flex-1 min-w-[200px]">
                    <p class="font-medium">{{ $m['judul'] }} <span class="text-[11px] ml-1 px-2 py-0.5 rounded-full bg-[#F5F3EC] text-[#5C6B62]">{{ $m['jenis'] }}</span></p>
                    <p class="text-xs text-[#5C6B62]">{{ $m['nomor'] }} · ditandatangani {{ $m['waktu']->translatedFormat('d M Y') }}</p>
                </div>

                @if ($m['terkirim'])
                    <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Terkirim ke petani</span>
                @else
                    <span class="text-xs px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200">Menunggu admin</span>
                @endif

                <div class="flex items-center gap-1.5">
                    <a href="{{ route('ketua.surat.scan', ['jenis' => $m['kunci'], 'id' => $m['id']]) }}" target="_blank" title="Lihat scan"
                       class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full border border-[#E1DCC9] text-[#1B4332] text-xs font-medium hover:border-[#2D6A4F] transition">
                        <i class="bi bi-eye"></i> Lihat
                    </a>

                    @unless ($m['terkirim'])
                        <form method="POST" action="{{ route('ketua.surat.hapus-scan', ['jenis' => $m['kunci'], 'id' => $m['id']]) }}"
                              onsubmit="return confirm('Hapus scan {{ $m['nomor'] }}? Dokumen kembali ke daftar menunggu dan scan harus diunggah ulang.')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus scan"
                                    class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full border border-red-200 text-red-600 text-xs font-medium hover:bg-red-50 transition">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    @endunless
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-sm text-[#5C6B62]">Belum ada.</div>
        @endforelse
    </div>

@endsection