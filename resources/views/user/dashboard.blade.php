@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

    @php
        $jam = now()->hour;
        $sapaan = match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $badgeStatus = [
            'diajukan'  => ['label' => 'Diajukan',  'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
            'pending'   => ['label' => 'Menunggu',  'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
            'diproses'  => ['label' => 'Diproses',  'class' => 'bg-sky-50 text-sky-800 border-sky-200'],
            'diterima'  => ['label' => 'Terbit',    'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
            'ditolak'   => ['label' => 'Ditolak',   'class' => 'bg-red-50 text-red-800 border-red-200'],
        ];
    @endphp

    {{-- Header Sapaan & Aksi Cepat --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-[#5C6B62]">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h1 class="text-2xl sm:text-3xl font-semibold text-[#1B4332] mt-1">
                {{ $sapaan }}, {{ explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-sm text-[#5C6B62] mt-0.5">
                Ringkasan layanan rekomendasi BBM Solar, Pupuk Bersubsidi, dan kelembagaan Poktan Anda.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('user.surat-solar.create') }}"
               class="inline-flex items-center gap-2 rounded-full bg-[#1B4332] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2D6A4F] transition shadow-2xs">
                <i class="bi bi-plus-circle"></i>
                <span>Ajukan Solar</span>
            </a>
            <a href="{{ route('user.surat-pupuk.create') }}"
               class="inline-flex items-center gap-2 rounded-full border border-[#E1DCC9] bg-white px-4 py-2.5 text-sm font-medium text-[#1B4332] hover:border-[#2D6A4F] hover:bg-[#EAF2EC]/50 transition">
                <i class="bi bi-flower1"></i>
                <span>Ajukan Pupuk</span>
            </a>
        </div>
    </div>

    {{-- 4 Kartu KPI Utama --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-[#5C6B62]">Total Pengajuan</span>
                <span class="w-8 h-8 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-sm">
                    <i class="bi bi-folder2-open"></i>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-semibold text-[#1B4332]">{{ $totalPengajuan }}</p>
            <p class="text-xs text-[#8C9086] mt-1">{{ $jumlahSolar }} Solar · {{ $jumlahPupuk }} Pupuk</p>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-[#5C6B62]">Surat Terbit</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="bi bi-check-circle"></i>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-semibold text-emerald-700">{{ $jumlahDiterima }}</p>
            <p class="text-xs text-[#8C9086] mt-1">Siap unduh & digunakan</p>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-[#5C6B62]">Sedang Diproses</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm">
                    <i class="bi bi-hourglass-split"></i>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-semibold text-amber-700">{{ $jumlahProses }}</p>
            <p class="text-xs text-[#8C9086] mt-1">Verifikasi & tanda tangan</p>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-[#5C6B62]">Perlu Perbaikan</span>
                <span class="w-8 h-8 rounded-xl bg-red-50 text-red-700 flex items-center justify-center text-sm">
                    <i class="bi bi-exclamation-circle"></i>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-semibold text-red-700">{{ $jumlahDitolak }}</p>
            <p class="text-xs text-[#8C9086] mt-1">Status ditolak admin</p>
        </div>
    </div>

    {{-- Banner Status Kelembagaan Kelompok Tani (Poktan) --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 sm:p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
            <span class="w-11 h-11 rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-people-fill"></i>
            </span>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-xs font-medium uppercase tracking-wider text-[#5C6B62]">Kelembagaan Kelompok Tani</p>
                    @if ($poktanAktif)
                        @php $stPoktan = $badgeStatus[$poktanAktif->status] ?? ['label' => ucfirst($poktanAktif->status), 'class' => 'bg-slate-100 text-slate-700 border-slate-200']; @endphp
                        <span class="text-[11px] font-medium px-2.5 py-0.5 rounded-full border {{ $stPoktan['class'] }}">
                            {{ $stPoktan['label'] }}
                        </span>
                    @endif
                </div>

                @if ($poktanAktif)
                    <p class="font-semibold text-[#14231C] mt-0.5">
                        {{ $poktanAktif->nama_kelompok }}
                        <span class="text-xs font-normal text-[#5C6B62] capitalize">({{ $jabatanPoktan }} · Desa {{ $poktanAktif->desa?->nama ?? '—' }})</span>
                    </p>
                    <p class="text-xs text-[#5C6B62] mt-0.5">
                        Total Lahan: <b>{{ number_format((float) $poktanAktif->luas_lahan_ha, 2, ',', '.') }} ha</b>
                        @if ($poktanAktif->nomor_sk) · No. SK: <b>{{ $poktanAktif->nomor_sk }}</b> @endif
                    </p>
                @else
                    <p class="font-semibold text-[#14231C] mt-0.5">Belum Tergabung dalam Kelompok Tani</p>
                    <p class="text-xs text-[#5C6B62] mt-0.5">
                        Pengajuan BBM Solar tetap bisa dilakukan secara mandiri. Untuk Pupuk Bersubsidi, silakan gabung atau bentuk Poktan terlebih dahulu.
                    </p>
                @endif
            </div>
        </div>

        <a href="{{ route('user.poktan') }}"
           class="inline-flex items-center justify-center gap-1.5 rounded-full border border-[#E1DCC9] bg-[#FAF9F4] px-4 py-2 text-xs font-semibold text-[#1B4332] hover:border-[#2D6A4F] hover:bg-white transition shrink-0">
            <span>{{ $poktanAktif ? 'Kelola Poktan' : 'Daftar / Gabung Poktan' }}</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    {{-- Bagian Visualisasi Grafik (Diagram Bulat & Grafik Batang) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

        {{-- Diagram Bulat (Donut Chart): Proporsi Status Pengajuan --}}
        <div class="lg:col-span-5 bg-white border border-[#E1DCC9] rounded-2xl p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <h2 class="font-semibold text-[#1B4332]">Proporsi Status Surat</h2>
                <p class="text-xs text-[#5C6B62] mt-0.5">Distribusi seluruh pengajuan rekomendasi Anda</p>
            </div>

            @if ($totalPengajuan > 0)
                <div class="relative mx-auto my-5 w-48 h-48">
                    <canvas id="chartStatusDonut"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-bold text-[#1B4332]">{{ $totalPengajuan }}</span>
                        <span class="text-[11px] text-[#5C6B62]">Total Surat</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-3 border-t border-[#E1DCC9] text-center text-xs">
                    <div>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#2D6A4F] mr-1"></span>
                        <span class="text-[#5C6B62]">Terbit</span>
                        <p class="font-semibold text-[#14231C] mt-0.5">{{ $jumlahDiterima }}</p>
                    </div>
                    <div>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#D97706] mr-1"></span>
                        <span class="text-[#5C6B62]">Proses</span>
                        <p class="font-semibold text-[#14231C] mt-0.5">{{ $jumlahProses }}</p>
                    </div>
                    <div>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#DC2626] mr-1"></span>
                        <span class="text-[#5C6B62]">Ditolak</span>
                        <p class="font-semibold text-[#14231C] mt-0.5">{{ $jumlahDitolak }}</p>
                    </div>
                </div>
            @else
                <div class="my-8 text-center py-6 px-4 rounded-2xl bg-[#FAF9F4] border border-dashed border-[#E1DCC9]">
                    <i class="bi bi-pie-chart text-3xl text-[#8C9086]"></i>
                    <p class="text-sm font-medium text-[#14231C] mt-2">Belum ada data grafik</p>
                    <p class="text-xs text-[#5C6B62] mt-1">Diagram status akan muncul otomatis setelah Anda membuat pengajuan pertama.</p>
                </div>
            @endif
        </div>

        {{-- Grafik Batang (Bar Chart): Tren Pengajuan 6 Bulan Terakhir --}}
        <div class="lg:col-span-7 bg-white border border-[#E1DCC9] rounded-2xl p-5 sm:p-6 flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2 mb-4">
                <div>
                    <h2 class="font-semibold text-[#1B4332]">Aktivitas Pengajuan (6 Bulan Terakhir)</h2>
                    <p class="text-xs text-[#5C6B62] mt-0.5">Perbandingan pengajuan BBM Solar dan Pupuk Bersubsidi per bulan</p>
                </div>
                <div class="flex items-center gap-3 text-xs shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[#5C6B62]">
                        <span class="w-2.5 h-2.5 rounded-xs bg-[#1B4332]"></span> Solar
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[#5C6B62]">
                        <span class="w-2.5 h-2.5 rounded-xs bg-[#C9A227]"></span> Pupuk
                    </span>
                </div>
            </div>

            <div class="relative h-56 w-full">
                <canvas id="chartTrenBulanan"></canvas>
            </div>
        </div>
    </div>

    {{-- Tabel Ringkasan Pengajuan Terbaru --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-[#E1DCC9] flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-[#1B4332]">Ringkasan Pengajuan Terbaru</h2>
                <p class="text-xs text-[#5C6B62]">5 permohonan rekomendasi terakhir Anda</p>
            </div>
            <a href="{{ route('user.surat-pengajuan.index') }}" class="text-xs font-semibold text-[#2D6A4F] hover:underline inline-flex items-center gap-1">
                <span>Lihat Semua</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if ($riwayatTerbaru->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#FAF9F4] border-b border-[#E1DCC9] text-xs font-semibold uppercase tracking-wider text-[#5C6B62]">
                            <th class="py-3 px-5">Jenis Layanan</th>
                            <th class="py-3 px-4">Nomor Surat</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E1DCC9]">
                        @foreach ($riwayatTerbaru as $item)
                            @php
                                $st = $badgeStatus[$item['status']] ?? ['label' => ucfirst($item['status']), 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                            @endphp
                            <tr class="hover:bg-[#FAF9F4]/70 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-base shrink-0">
                                            <i class="bi {{ $item['ikon'] }}"></i>
                                        </span>
                                        <div>
                                            <p class="font-medium text-[#14231C]">{{ $item['jenis'] }}</p>
                                            <p class="text-xs text-[#5C6B62]">{{ $item['keterangan'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-medium text-[#14231C] whitespace-nowrap">
                                    {{ $item['nomor'] }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-[#5C6B62] whitespace-nowrap">
                                    {{ $item['tanggal']?->translatedFormat('d M Y') ?? '—' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $st['class'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        @if ($item['url_resmi'])
                                            <a href="{{ $item['url_resmi'] }}"
                                               class="inline-flex items-center gap-1 rounded-full bg-[#1B4332] px-3 py-1 text-xs font-medium text-white hover:bg-[#2D6A4F] transition">
                                                <i class="bi bi-file-earmark-pdf"></i> Surat Resmi
                                            </a>
                                        @endif
                                        <a href="{{ $item['url_detail'] }}"
                                           class="inline-flex items-center gap-1 rounded-full border border-[#E1DCC9] bg-white px-3 py-1 text-xs font-medium text-[#1B4332] hover:border-[#2D6A4F] transition">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <p class="text-sm font-medium text-[#14231C]">Belum ada riwayat pengajuan</p>
                <p class="text-xs text-[#5C6B62] mt-1 mb-4">Mulai ajukan surat rekomendasi BBM Solar atau Pupuk Bersubsidi secara daring.</p>
                <a href="{{ route('user.surat-solar.create') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-[#1B4332] px-5 py-2 text-xs font-medium text-white hover:bg-[#2D6A4F]">
                    <i class="bi bi-plus-lg"></i> Buat Pengajuan Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- Menu Akses Cepat Layanan --}}
    <h2 class="text-sm font-semibold text-[#5C6B62] mb-3">Akses Cepat Layanan</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <a href="{{ route('user.surat-solar.index') }}" class="group bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] rounded-2xl p-4 transition flex items-start gap-3.5">
            <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0 group-hover:bg-[#1B4332] group-hover:text-white transition">
                <i class="bi bi-fuel-pump"></i>
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-sm text-[#14231C] group-hover:text-[#1B4332]">Surat Solar</p>
                <p class="text-xs text-[#5C6B62] mt-0.5 line-clamp-2">Permohonan & unduh surat BBM subsidi</p>
            </div>
        </a>

        <a href="{{ route('user.surat-pupuk.index') }}" class="group bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] rounded-2xl p-4 transition flex items-start gap-3.5">
            <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0 group-hover:bg-[#1B4332] group-hover:text-white transition">
                <i class="bi bi-flower1"></i>
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-sm text-[#14231C] group-hover:text-[#1B4332]">Surat Pupuk</p>
                <p class="text-xs text-[#5C6B62] mt-0.5 line-clamp-2">Rekomendasi pupuk RDKK kelompok tani</p>
            </div>
        </a>

        <a href="{{ route('user.profil') }}" class="group bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] rounded-2xl p-4 transition flex items-start gap-3.5">
            <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0 group-hover:bg-[#1B4332] group-hover:text-white transition">
                <i class="bi bi-person-badge"></i>
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-sm text-[#14231C] group-hover:text-[#1B4332]">Profil & NIK</p>
                <p class="text-xs text-[#5C6B62] mt-0.5 line-clamp-2">Lengkapi identitas & kontak akun Anda</p>
            </div>
        </a>

        <a href="{{ route('user.kritiksaran.create') }}" class="group bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] rounded-2xl p-4 transition flex items-start gap-3.5">
            <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0 group-hover:bg-[#1B4332] group-hover:text-white transition">
                <i class="bi bi-chat-square-text"></i>
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-sm text-[#14231C] group-hover:text-[#1B4332]">Kritik & Saran</p>
                <p class="text-xs text-[#5C6B62] mt-0.5 line-clamp-2">Sampaikan aspirasi pelayanan UPTD</p>
            </div>
        </a>
    </div>

    {{-- Chart.js untuk Diagram Bulat & Grafik Tren --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fontFamily = "'Plus Jakarta Sans', 'Inter', system-ui, sans-serif";

            // 1. Diagram Bulat (Donut Chart)
            const donutEl = document.getElementById('chartStatusDonut');
            if (donutEl) {
                new Chart(donutEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Surat Terbit', 'Sedang Diproses', 'Ditolak'],
                        datasets: [{
                            data: [{{ $jumlahDiterima }}, {{ $jumlahProses }}, {{ $jumlahDitolak }}],
                            backgroundColor: ['#2D6A4F', '#D97706', '#DC2626'],
                            borderWidth: 3,
                            borderColor: '#FFFFFF',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#14231C',
                                titleFont: { family: fontFamily, size: 12 },
                                bodyFont: { family: fontFamily, size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }

            // 2. Grafik Batang Tren 6 Bulan Terakhir
            const trenEl = document.getElementById('chartTrenBulanan');
            if (trenEl) {
                new Chart(trenEl, {
                    type: 'bar',
                    data: {
                        labels: @json($labelBulan),
                        datasets: [
                            {
                                label: 'BBM Solar',
                                data: @json($dataSolarBulanan),
                                backgroundColor: '#1B4332',
                                borderRadius: 6,
                                maxBarThickness: 22
                            },
                            {
                                label: 'Pupuk Subsidi',
                                data: @json($dataPupukBulanan),
                                backgroundColor: '#C9A227',
                                borderRadius: 6,
                                maxBarThickness: 22
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#14231C',
                                titleFont: { family: fontFamily, size: 12 },
                                bodyFont: { family: fontFamily, size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#5C6B62', font: { family: fontFamily, size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    color: '#5C6B62',
                                    font: { family: fontFamily, size: 11 }
                                },
                                grid: { color: '#F1EFE7' }
                            }
                        }
                    }
                });
            }
        });
    </script>

@endsection