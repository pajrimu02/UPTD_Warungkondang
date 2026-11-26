<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengajuan e-RDKK Pupuk Bersubsidi</title>
    <style>
        @page { size: A4 landscape; margin: 1.1cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 9.5pt; color: #000; line-height: 1.3; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 10px; }
        .kop h3 { margin: 0; font-size: 10.5pt; text-transform: uppercase; font-weight: bold; }
        .kop h2 { margin: 2px 0 0; font-size: 12.5pt; text-transform: uppercase; }
        .kop p { margin: 2px 0 0; font-size: 9pt; }
        .dasar { font-size: 8.5pt; margin-bottom: 8px; }
        .meta-table { width: 100%; margin-bottom: 8px; border-collapse: collapse; font-size: 9pt; }
        .meta-table td { padding: 1.5px 4px; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 7.6pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 3px 3px; text-align: center; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .ttd-table { width: 100%; margin-top: 14px; border-collapse: collapse; text-align: center; font-size: 9.5pt; page-break-inside: avoid; }
        .ttd-table td { width: 33.3%; vertical-align: top; }
        .ttd-space { height: 48px; }
    </style>
</head>
<body>
@php
    $surat = $suratPupuk;
    $poktan = $surat->poktan;
    $namaDesa = $poktan->desa?->nama_desa ?? $poktan->desa?->nama ?? config('uptd.tempat', 'Warungkondang');
    $subsektorUtama = $surat->items->pluck('subsektor')->unique()->implode(', ') ?: 'Tanaman Pangan';
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',') ?: '0';
@endphp

    <div class="kop">
        <h3>PEMERINTAH KABUPATEN {{ strtoupper(config('uptd.kabupaten', 'CIANJUR')) }} · {{ strtoupper(config('uptd.nama', 'UPTD PELAYANAN PERTANIAN WARUNGKONDANG')) }}</h3>
        <h2>RENCANA DEFINITIF KEBUTUHAN KELOMPOK (e-RDKK) PUPUK BERSUBSIDI</h2>
        <p>Nomor Surat: <strong>{{ $surat->nomor_surat ?? '-' }}</strong> · Alokasi Tahun Tanam {{ $surat->created_at?->format('Y') ?? date('Y') }}</p>
    </div>

    <p class="dasar">
        <strong>Dasar Hukum:</strong> Peraturan Menteri Pertanian Nomor 10 Tahun 2022 jo. Peraturan Menteri Pertanian Nomor 01 Tahun 2024 tentang Tata Cara Penetapan Alokasi dan Harga Eceran Tertinggi Pupuk Bersubsidi Sektor Pertanian.
    </p>

    <table class="meta-table">
        <tr>
            <td width="16%">ID / Nama Poktan</td>
            <td width="34%">: <strong>{{ str_pad((string) $poktan->id, 4, '0', STR_PAD_LEFT) }} — {{ $poktan->nama_kelompok }}</strong></td>
            <td width="18%">Desa / Kode Desa</td>
            <td width="32%">: {{ $namaDesa }} ({{ $surat->kode_desa ?? $poktan->desa?->kode_desa ?? '-' }})</td>
        </tr>
        <tr>
            <td>Gapoktan</td>
            <td>: {{ $surat->gapoktan ?? '-' }}</td>
            <td>Kode Kios Pengecer</td>
            <td>: {{ $surat->kode_kios ?? '-' }}</td>
        </tr>
        <tr>
            <td>Subsektor</td>
            <td>: {{ $subsektorUtama }}</td>
            <td>Nama Kios Pengecer</td>
            <td>: {{ $surat->nama_kios ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Penyuluh (PPL)</td>
            <td>: {{ $surat->nama_penyuluh }}</td>
            <td>Kecamatan / Kabupaten</td>
            <td>: {{ config('uptd.kecamatan', 'Warungkondang') }} / {{ config('uptd.kabupaten', 'Cianjur') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="2.5%">No</th>
                <th rowspan="2" width="13%">Nama Petani / NIK</th>
                <th rowspan="2" width="12%">Tempat, Tgl Lahir &amp;<br>Nama Ibu Kandung</th>
                <th rowspan="2" width="10%">Alamat</th>
                <th colspan="7">Periode Tanam I (MT-1)</th>
                <th colspan="7">Periode Tanam II (MT-2)</th>
                <th colspan="7">Periode Tanam III (MT-3)</th>
            </tr>
            <tr>
                <th>Komoditas</th>
                <th>Luas<br>(Ha)</th>
                <th>Urea<br>(kg)</th>
                <th>NPK<br>(kg)</th>
                <th>NPK Form.<br>(kg)</th>
                <th>Org<br>(kg)</th>
                <th>ZA<br>(kg)</th>

                <th>Komoditas</th>
                <th>Luas<br>(Ha)</th>
                <th>Urea<br>(kg)</th>
                <th>NPK<br>(kg)</th>
                <th>NPK Form.<br>(kg)</th>
                <th>Org<br>(kg)</th>
                <th>ZA<br>(kg)</th>

                <th>Komoditas</th>
                <th>Luas<br>(Ha)</th>
                <th>Urea<br>(kg)</th>
                <th>NPK<br>(kg)</th>
                <th>NPK Form.<br>(kg)</th>
                <th>Org<br>(kg)</th>
                <th>ZA<br>(kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($surat->items as $index => $item)
                @php
                    $mt1 = $item->musim[0] ?? [];
                    $mt2 = $item->musim[1] ?? [];
                    $mt3 = $item->musim[2] ?? [];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $item->nama_petani }}</strong><br>
                        NIK: {{ $item->nik }}
                    </td>
                    <td class="text-left">
                        {{ $item->tempat_lahir }}, {{ $item->tanggal_lahir?->format('d-m-Y') }}<br>
                        Ibu: {{ $item->nama_ibu_kandung }}
                    </td>
                    <td class="text-left">{{ $item->alamat }}</td>

                    <!-- MT 1 -->
                    <td>{{ $mt1['komoditas'] ?? '-' }}</td>
                    <td>{{ $fmt($mt1['luas'] ?? 0) }}</td>
                    <td>{{ $fmt($mt1['urea'] ?? 0) }}</td>
                    <td>{{ $fmt($mt1['npk'] ?? 0) }}</td>
                    <td>{{ $fmt($mt1['npk_formula'] ?? 0) }}</td>
                    <td>{{ $fmt($mt1['organik'] ?? 0) }}</td>
                    <td>{{ $fmt($mt1['za'] ?? 0) }}</td>

                    <!-- MT 2 -->
                    <td>{{ $mt2['komoditas'] ?? '-' }}</td>
                    <td>{{ $fmt($mt2['luas'] ?? 0) }}</td>
                    <td>{{ $fmt($mt2['urea'] ?? 0) }}</td>
                    <td>{{ $fmt($mt2['npk'] ?? 0) }}</td>
                    <td>{{ $fmt($mt2['npk_formula'] ?? 0) }}</td>
                    <td>{{ $fmt($mt2['organik'] ?? 0) }}</td>
                    <td>{{ $fmt($mt2['za'] ?? 0) }}</td>

                    <!-- MT 3 -->
                    <td>{{ $mt3['komoditas'] ?? '-' }}</td>
                    <td>{{ $fmt($mt3['luas'] ?? 0) }}</td>
                    <td>{{ $fmt($mt3['urea'] ?? 0) }}</td>
                    <td>{{ $fmt($mt3['npk'] ?? 0) }}</td>
                    <td>{{ $fmt($mt3['npk_formula'] ?? 0) }}</td>
                    <td>{{ $fmt($mt3['organik'] ?? 0) }}</td>
                    <td>{{ $fmt($mt3['za'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 8.5pt; margin-top: 6px;">
        Demikian Rencana Definitif Kebutuhan Kelompok (e-RDKK) ini disusun sesuai data induk kependudukan (NIK &amp; Nama Ibu Kandung) serta kepemilikan lahan anggota kelompok tani yang sah untuk diinput ke sistem e-RDKK Kementerian Pertanian.
    </p>

    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                Penyuluh Pertanian Lapangan (PPL)
                <div class="ttd-space"></div>
                <strong><u>{{ $surat->nama_penyuluh }}</u></strong>
            </td>
            <td>
                Menyetujui,<br>
                {{ config('uptd.jabatan_penandatangan', 'Kepala Sub Bagian Tata Usaha') }}
                <div class="ttd-space"></div>
                <strong><u>{{ config('uptd.nama_penandatangan', 'Jamaludin Murdani, S.P.') }}</u></strong><br>
                NIP. {{ config('uptd.nip_penandatangan', '198811212017061001') }}
            </td>
            <td>
                {{ $namaDesa }}, {{ ($surat->disetujui_at ?? now())->translatedFormat('d F Y') }}<br>
                Ketua Kelompok Tani {{ $poktan->nama_kelompok }}
                <div class="ttd-space"></div>
                <strong><u>{{ $poktan->nama_ketua ?: '(....................................)' }}</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>