<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #14231C; }
        h1 { font-size: 14px; text-align: center; margin-bottom: 4px; }
        .sub { text-align: center; font-size: 10px; color: #5C6B62; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 4px; border-bottom: 1px solid #E1DCC9; }
        td.no { width: 25px; color: #5C6B62; }
        td.label { width: 220px; font-weight: bold; }
        td.sep { width: 15px; }
        .section { background: #F5F3EC; font-weight: bold; padding: 6px; margin-top: 15px; margin-bottom: 5px; }
    </style>
</head>
<body>

    <h1>LEMBAR KERJA — INPUT KE APK SOLAR</h1>
    <p class="sub">Pengajuan #{{ $suratSolar->id }} — dicetak {{ now()->translatedFormat('d F Y, H:i') }}</p>

    <div class="section">A. Format Permohonan Surat Rekomendasi (Perorangan)</div>
    <table>
        <tr><td class="no">1</td><td class="label">Nama</td><td class="sep">:</td><td>{{ $suratSolar->nama_pemohon ?? $suratSolar->user->name }}</td></tr>
        <tr><td class="no">2</td><td class="label">NIK</td><td class="sep">:</td><td>{{ $suratSolar->nik }}</td></tr>
        <tr><td class="no">3</td><td class="label">Alamat</td><td class="sep">:</td><td>{{ $suratSolar->alamat }}</td></tr>
        <tr><td class="no">4</td><td class="label">Konsumen Pengguna</td><td class="sep">:</td><td>{{ ucwords(str_replace('_',' ',$suratSolar->status_konsumen)) }}</td></tr>
        <tr><td class="no">5</td><td class="label">Jenis Usaha</td><td class="sep">:</td><td>{{ $suratSolar->jenis_usaha }}</td></tr>
        @if ($suratSolar->nama_kapal)
        <tr><td class="no">6</td><td class="label">Nama Kapal</td><td class="sep">:</td><td>{{ $suratSolar->nama_kapal }}</td></tr>
        @endif
    </table>

    <div class="section">B. Data Alat/Mesin & Usulan Konsumsi</div>
    <table>
        <tr><td class="no">1</td><td class="label">Jenis Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->jenis_alat_mesin }}</td></tr>
        <tr><td class="no">2</td><td class="label">Fungsi Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->fungsi_alat_mesin }}</td></tr>
        <tr><td class="no">3</td><td class="label">Jumlah Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->jumlah_alat_mesin }}</td></tr>
        <tr><td class="no">4</td><td class="label">Daya Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->daya_alat_mesin ?: '-' }}</td></tr>
        <tr><td class="no">5</td><td class="label">Lama Penggunaan Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->lama_penggunaan ?: '-' }}</td></tr>
        <tr><td class="no">6</td><td class="label">Lama Operasi Alat/Mesin</td><td class="sep">:</td><td>{{ $suratSolar->lama_operasi ?: '-' }}</td></tr>
        <tr><td class="no">7</td><td class="label">Usulan Volume Konsumsi</td><td class="sep">:</td><td>{{ $suratSolar->usulan_volume_konsumsi }} Liter / {{ $suratSolar->volume_periode }}</td></tr>
        <tr><td class="no">8</td><td class="label">Estimasi Sisa JBT/JBKP</td><td class="sep">:</td><td>{{ $suratSolar->estimasi_sisa_liter ?? '-' }} Liter</td></tr>
    </table>

    <div class="section">Rekomendasi Sistem</div>
    <table>
        <tr><td class="no"></td><td class="label">Skor SPK</td><td class="sep">:</td><td>{{ $suratSolar->spk_skor ?? '-' }}/100 ({{ ucfirst(str_replace('_',' ',$suratSolar->spk_label ?? '-')) }})</td></tr>
        <tr><td class="no"></td><td class="label">Berkas Terlampir</td><td class="sep">:</td><td>
            @foreach (['file_ktp'=>'KTP','file_sku'=>'SKU','file_foto_mesin'=>'Foto Mesin'] as $f => $l)
                {{ $suratSolar->$f ? '✓ '.$l : '✗ '.$l }}{{ !$loop->last ? ' · ' : '' }}
            @endforeach
        </td></tr>
    </table>

</body>
</html>