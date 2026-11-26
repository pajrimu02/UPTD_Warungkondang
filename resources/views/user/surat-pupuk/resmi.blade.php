@extends('layouts.user')

@section('title', 'Surat Resmi Pupuk')

@section('content')

    @php
        $namaPoktan = \Illuminate\Support\Str::slug($data->poktan?->nama_kelompok ?? $data->user?->name ?? 'Poktan');
        $ext = $data->file_surat_resmi ? strtolower(pathinfo($data->file_surat_resmi, PATHINFO_EXTENSION)) : 'pdf';
        $namaDownload = 'Surat-Rekomendasi-Pupuk-Subsidi-' . $namaPoktan . '.' . $ext;
        $fileUrl = $data->file_surat_resmi ? asset('storage/' . $data->file_surat_resmi) : null;
        $sudahTerbit = $data->status === 'diterima' && $data->diserahkan_at && $fileUrl;
    @endphp

    <a href="{{ route('user.surat-pupuk.index', $data) }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali </span>
    </a>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Surat Rekomendasi Resmi</h1>
            <p class="text-sm text-[#5C6B62]">Nomor: {{ $data->nomor_surat ?? 'Belum diterbitkan' }}</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($sudahTerbit)
                <a href="{{ $fileUrl }}" target="_blank"
                   class="inline-flex items-center gap-1.5 border border-[#E1DCC9] bg-white text-[#1B4332] text-sm px-4 py-2.5 rounded-full hover:border-[#2D6A4F]">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Buka Penuh</span>
                </a>
                <a href="{{ $fileUrl }}" download="{{ $namaDownload }}"
                   class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
                    <i class="bi bi-download"></i>
                    <span>Unduh</span>
                </a>
            @else
                @include('user.surat-pengajuan._badge', ['status' => $data->status])
            @endif
        </div>
    </div>

    @if ($sudahTerbit)
        <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
            @if ($ext === 'pdf')
                {{-- Renderer PDF berbasis Canvas (Kompatibel 100% di Chrome HP & Desktop) --}}
                <div id="pdf-loading" class="p-10 text-center text-sm text-[#5C6B62]">
                    <i class="bi bi-hourglass-split text-xl block mb-2 text-[#2D6A4F]"></i>
                    Memuat dokumen surat resmi...
                </div>

                <div id="pdf-error" class="hidden p-8 text-center">
                    <p class="text-sm text-[#5C6B62] mb-4">Pratinjau langsung tidak didukung browser ini. Silakan buka atau unduh file PDF.</p>
                    <a href="{{ $fileUrl }}" download="{{ $namaDownload }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-5 py-2.5 rounded-full">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh File PDF
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
                <img src="{{ $fileUrl }}" alt="Surat Rekomendasi Pupuk" class="w-full h-auto">
            @endif
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-8 text-center">
            @if ($data->status === 'ditolak')
                <span class="w-14 h-14 rounded-full bg-red-50 text-red-700 flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-x-circle"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Pengajuan Ditolak</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-md mx-auto">
                    {{ $data->catatan_admin ?? 'Tidak ada catatan dari admin.' }}
                </p>
            @else
                <span class="w-14 h-14 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-hourglass-split"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Surat Rekomendasi Belum Tersedia</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-md mx-auto">
                    @if ($data->ttd_kembali_at)
                        Surat sudah bertanda tangan dan sedang disiapkan admin untuk dikirim ke akun Anda.
                    @elseif ($data->ttd_dikirim_at || $data->status === 'diproses')
                        Pengajuan disetujui. Surat sedang dalam proses penandatanganan pejabat berwenang.
                    @else
                        Pengajuan Anda masih dalam tahap verifikasi admin UPTD.
                    @endif
                </p>
            @endif
        </div>
    @endif

    @if ($data->catatan_admin && $data->status !== 'ditolak')
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5 mt-5">
            <p class="text-sm font-medium text-[#1B4332] mb-1">Catatan Admin</p>
            <p class="text-sm text-[#5C6B62]">{{ $data->catatan_admin }}</p>
        </div>
    @endif

@endsection