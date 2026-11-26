@extends('layouts.user')

@section('title', 'SK Pengukuhan Poktan')

@section('content')

    @php
        $namaPoktan = \Illuminate\Support\Str::slug($data->nama_kelompok ?? 'Poktan');
        $ext = $data->file_sk_resmi ? strtolower(pathinfo($data->file_sk_resmi, PATHINFO_EXTENSION)) : 'pdf';
        $namaDownload = 'SK-Pengukuhan-Poktan-' . $namaPoktan . '.' . $ext;
        $fileUrl = $data->file_sk_resmi ? asset('storage/' . $data->file_sk_resmi) : null;
        $sudahTerbit = $data->status === 'disetujui' && $data->diserahkan_at && $fileUrl;
    @endphp
    <div class="mb-5">
        <x-tombol-kembali :href="route('user.poktan')" />
    </div>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">SK Pengukuhan Kelompok Tani</h1>
            <p class="text-sm text-[#5C6B62] mt-1">
                {{ $data->nama_kelompok }} · Nomor SK: <b class="text-[#1B4332]">{{ $data->nomor_sk ?? 'Belum diterbitkan' }}</b>
            </p>
        </div>

        @if ($sudahTerbit)
            <div class="flex items-center gap-2">
                <a href="{{ $fileUrl }}" target="_blank"
                   class="inline-flex items-center gap-1.5 border border-[#E1DCC9] bg-white text-[#1B4332] text-sm px-4 py-2.5 rounded-full hover:border-[#2D6A4F]">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Buka Penuh</span>
                </a>
                <a href="{{ $fileUrl }}" download="{{ $namaDownload }}"
                   class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
                    <i class="bi bi-download"></i>
                    <span>Unduh SK</span>
                </a>
            </div>
        @endif
    </div>

    @if ($sudahTerbit)
        <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
            @if ($ext === 'pdf')
                <div id="pdf-loading" class="p-10 text-center text-sm text-[#5C6B62]">
                    <i class="bi bi-hourglass-split text-xl block mb-2 text-[#2D6A4F]"></i>
                    Memuat dokumen SK Pengukuhan...
                </div>

                <div id="pdf-error" class="hidden p-8 text-center">
                    <p class="text-sm text-[#5C6B62] mb-4">Pratinjau langsung tidak didukung browser ini. Silakan unduh file SK.</p>
                    <a href="{{ $fileUrl }}" download="{{ $namaDownload }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-5 py-2.5 rounded-full">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh File SK
                    </a>
                </div>

                <div id="pdf-pages" class="p-3 sm:p-6 bg-[#F5F3EC] space-y-4 flex flex-col items-center"></div>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                <script>
                    (function () {
                        const url = @json($fileUrl);
                        const container = document.getElementById('pdf-pages');
                        const loading = document.getElementById('pdf-loading');
                        const errorBox = document.getElementById('pdf-error');

                        if (!window.pdfjsLib) {
                            loading.classList.add('hidden');
                            errorBox.classList.remove('hidden');
                            return;
                        }

                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                        pdfjsLib.getDocument(url).promise.then(async function (pdf) {
                            loading.classList.add('hidden');
                            const maxWidth = Math.min(container.clientWidth - 24, 820);

                            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                                const page = await pdf.getPage(pageNum);
                                const unscaledViewport = page.getViewport({ scale: 1 });
                                const scale = (maxWidth / unscaledViewport.width) * (window.devicePixelRatio || 1);
                                const viewport = page.getViewport({ scale: scale });

                                const canvas = document.createElement('canvas');
                                canvas.width = viewport.width;
                                canvas.height = viewport.height;
                                canvas.style.width = Math.round(viewport.width / (window.devicePixelRatio || 1)) + 'px';
                                canvas.style.height = Math.round(viewport.height / (window.devicePixelRatio || 1)) + 'px';
                                canvas.className = 'bg-white shadow-sm rounded-lg border border-[#E1DCC9] max-w-full h-auto';

                                container.appendChild(canvas);
                                await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                            }
                        }).catch(function () {
                            loading.classList.add('hidden');
                            errorBox.classList.remove('hidden');
                        });
                    })();
                </script>
            @else
                <img src="{{ $fileUrl }}" alt="SK Pengukuhan Poktan" class="w-full h-auto">
            @endif
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-8 text-center">
            @if ($data->status === 'ditolak')
                <span class="w-14 h-14 rounded-full bg-red-50 text-red-700 flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-x-circle"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Pengajuan Poktan Ditolak</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-md mx-auto">
                    {{ $data->catatan_admin ?? 'Tidak ada catatan dari admin.' }}
                </p>
            @else
                <span class="w-14 h-14 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-hourglass-split"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">SK Pengukuhan Belum Tersedia</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-md mx-auto">
                    @if ($data->ttd_kembali_at)
                        SK sudah ditandatangani Kepala UPTD dan sedang disiapkan admin untuk diserahkan ke Poktan.
                    @elseif ($data->ttd_dikirim_at)
                        Poktan telah disetujui (Nomor SK: <b>{{ $data->nomor_sk }}</b>). Dokumen SK sedang dalam proses penandatanganan Kepala UPTD.
                    @else
                        Pengajuan Poktan masih dalam tahap verifikasi admin UPTD.
                    @endif
                </p>
            @endif
        </div>
    @endif

@endsection