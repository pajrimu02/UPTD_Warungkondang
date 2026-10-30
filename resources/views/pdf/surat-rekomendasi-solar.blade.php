<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1.5cm 2cm 1.5cm 2cm; }
        body { font-family: "Times New Roman", serif; font-size: 11px; color: #000; line-height: 1.35; margin: 0; }
        h1 { font-size: 14px; text-align: center; margin: 0 0 2px; text-decoration: underline; }
        .nomor { text-align: center; font-weight: bold; margin: 0 0 10px; }
        p { margin: 0 0 4px; }
        ol { margin: 2px 0 6px 0; padding-left: 20px; text-align: justify; }
        ol li { margin-bottom: 3px; }
        table.data { border-collapse: collapse; }
        table.data td { padding: 1px 4px 1px 0; vertical-align: top; }
        table.data td.l { width: 150px; }
        table.mesin { width: 100%; border-collapse: collapse; margin: 4px 0 4px; }
        table.mesin th, table.mesin td { border: 1px solid #000; padding: 3px 4px; text-align: center; font-size: 10px; }
        table.ttd { width: 100%; margin-top: 14px; page-break-inside: avoid; }
        table.ttd td.kanan { width: 230px; text-align: center; vertical-align: top; }
    </style>
</head>
<body>
@php
    $s = $suratSolar;
    $angka = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ',') ?: '0';
    $labelMesin = [
        'traktor_roda_dua' => 'Traktor Roda Dua (Hand Tractor)',
        'traktor_roda_empat' => 'Traktor Roda Empat',
        'pompa_air' => 'Pompa Air',
        'rmu' => 'RMU (Rice Milling Unit)',
        'mesin_pemotong_rumput' => 'Mesin Pemotong Rumput',
        'power_thresher' => 'Power Thresher',
    ];
    $jenisMesin = $labelMesin[$s->jenis_alat_mesin] ?? $s->jenis_alat_mesin;

    $faktor = ['minggu' => 1, 'bulan' => 1 / 4, 'tiga_bulan' => 1 / 13][$s->volume_periode] ?? 1;
    $konsumsiMinggu = round((float) $s->usulan_volume_konsumsi * $faktor);

    $sektor = \App\Http\Controllers\Auth\RegisteredUserController::KONSUMEN[$s->status_konsumen]
        ?? ucwords(str_replace('_', ' ', (string) $s->status_konsumen));
    $nama = $s->nama_pemohon ?? $s->user->name;
@endphp

    <h1>SURAT REKOMENDASI</h1>
    <p class="nomor">Nomor: {{ $s->nomor_surat }}</p>

    <p><b>Dasar Hukum:</b></p>
    <ol>
        <li>Undang-Undang Nomor 22 Tahun 2001 tentang Minyak dan Gas Bumi sebagaimana telah diubah dengan Undang-Undang Nomor 6 Tahun 2023 tentang Penetapan Peraturan Pemerintah Pengganti Undang-Undang Nomor 2 Tahun 2022 tentang Cipta Kerja menjadi Undang-Undang.</li>
        <li>Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah sebagaimana telah beberapa kali diubah terakhir dengan Undang-Undang Nomor 6 Tahun 2023 tentang Penetapan Peraturan Pemerintah Pengganti Undang-Undang Nomor 2 Tahun 2022 tentang Cipta Kerja menjadi Undang-Undang.</li>
        <li>Peraturan Presiden Nomor 191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga Jual Eceran Bahan Bakar Minyak sebagaimana telah beberapa kali terakhir dengan Peraturan Presiden Nomor 117 Tahun 2021 tentang Perubahan Ketiga atas Peraturan Presiden Nomor 191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga Jual Eceran Bahan Bakar Minyak.</li>
        <li>Peraturan Badan Pengatur Hilir Minyak dan Gas Bumi Nomor 2 Tahun 2023 tentang Penerbitan Surat Rekomendasi Untuk Pembelian Jenis BBM Minyak Tertentu dan Jenis BBM Khusus Penugasan.</li>
    </ol>

    <p>Dengan ini memberikan rekomendasi kepada:</p>
    <table class="data">
        <tr><td class="l">Nama</td><td>: {{ strtoupper($nama) }}</td></tr>
        <tr><td class="l">NIK</td><td>: {{ $s->nik }}</td></tr>
        <tr><td class="l">Alamat</td><td>: {{ $s->alamat }}</td></tr>
        <tr><td class="l">Nama Usaha</td><td>: {{ strtoupper($s->jenis_usaha) }}</td></tr>
        <tr><td class="l">Sektor Konsumen Pengguna</td><td>: {{ $sektor }}</td></tr>
        <tr><td class="l">Jenis Usaha</td><td>: {{ $s->poktan ? 'Pertanian Per Anggota Kelompok Tani' : 'Perorangan' }}</td></tr>
        @if ($s->nama_kapal)
            <tr><td class="l">Nama Kapal</td><td>: {{ $s->nama_kapal }}</td></tr>
        @endif
    </table>

    <p style="margin-top:8px;">Berdasarkan hasil verifikasi dan evaluasi perhitungan:</p>
    <ol>
        <li>
            Kebutuhan Jenis BBM Tertentu yang digunakan untuk alat sebagai berikut:
            <table class="mesin">
                <thead>
                    <tr>
                        <th>Jenis Mesin</th><th>Fungsi Mesin</th><th>Jumlah Mesin</th><th>Daya Mesin</th>
                        <th>Jam Penggunaan Mesin per Hari</th><th>Lama Operasi Mesin (Hari/Minggu)</th><th>Konsumsi JBT (Liter/Minggu)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $jenisMesin }}</td>
                        <td>{{ $s->fungsi_alat_mesin }}</td>
                        <td>{{ $s->jumlah_alat_mesin }}</td>
                        <td>{{ $s->daya_alat_mesin ?: '-' }}</td>
                        <td>{{ $s->lama_penggunaan !== null ? $angka($s->lama_penggunaan) : '-' }}</td>
                        <td>{{ $s->lama_operasi !== null ? $angka($s->lama_operasi) : '-' }}</td>
                        <td>{{ $angka($konsumsiMinggu) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:left;"><b>Total</b></td>
                        <td><b>{{ $angka($konsumsiMinggu) }}</b></td>
                    </tr>
                </tbody>
            </table>
        </li>
        <li>
            Diberikan Jenis BBM Tertentu Jenis Minyak Solar (Gas Oil):
            <table class="data">
                <tr><td>a. Alokasi Volume</td><td>: {{ $angka($s->alokasi_volume) }} Liter per Minggu</td></tr>
                <tr><td>b. Tempat Pengambilan</td><td>: {{ $s->tempat_pengambilan }}</td></tr>
                <tr><td>c. Nomor Penyalur</td><td>: {{ $s->nomor_penyalur }}</td></tr>
                <tr><td>d. Alamat Penyalur</td><td>: {{ $s->alamat_penyalur }}</td></tr>
            </table>
        </li>
        <li>Alat Pembelian yang digunakan: {{ $s->alat_pembelian }}.</li>
        <li>Jangka Waktu pemberlakuan Surat Rekomendasi sampai dengan tanggal {{ $s->berlaku_sampai?->translatedFormat('d F Y') }}.</li>
        <li>Penyalur SPBU/SPBKB/SPBN/SPBUN wajib mencatat riwayat pembelian Konsumen Pengguna dalam buku sebagaimana terlampir.</li>
        <li>Surat Rekomendasi ini hanya berlaku untuk perorangan sesuai dengan identitas pemohon Surat Rekomendasi.</li>
        <li>Jenis BBM Tertentu yang diperoleh tidak untuk diperjualbelikan kembali.</li>
        <li>Apabila Surat Rekomendasi tidak dipergunakan sebagaimana mestinya dan tidak sesuai dengan ketentuan peraturan perundang-undangan, Surat Rekomendasi akan dicabut dan diproses secara hukum sesuai dengan ketentuan peraturan perundang-undangan.</li>
        <li>Surat Rekomendasi ini beserta lampirannya harus dilampirkan kembali saat perpanjangan atau pengajuan permohonan Surat Rekomendasi.</li>
    </ol>

    <table class="ttd">
        <tr>
            <td></td>
            <td class="kanan">
                <p>{{ config('uptd.tempat') }}, {{ ($s->disetujui_at ?? now())->translatedFormat('d F Y') }}</p>
                <p>{{ config('uptd.jabatan_penandatangan') }}</p>
                <br><br><br><br>
                <p><b><u>{{ config('uptd.nama_penandatangan') }}</u></b><br>NIP. {{ config('uptd.nip_penandatangan') }}</p>
            </td>
        </tr>
    </table>

</body>
</html>