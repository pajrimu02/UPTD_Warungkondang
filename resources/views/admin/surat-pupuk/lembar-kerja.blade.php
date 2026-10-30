<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #14231C; }
        h1 { font-size: 13px; text-align: center; margin-bottom: 2px; }
        .sub { text-align: center; font-size: 9px; color: #5C6B62; margin-bottom: 12px; }
        .section { background: #F5F3EC; font-weight: bold; padding: 5px; margin: 12px 0 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #C9C4AF; padding: 3px 4px; }
        th { background: #F5F3EC; text-align: left; }
        td.r, th.r { text-align: right; }
        table.info td { border: none; border-bottom: 1px solid #E1DCC9; }
        table.info td.l { width: 140px; font-weight: bold; }
    </style>
</head>
<body>
@php
    $fmt = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',') ?: '0';
    $pupuk = ['luas' => 'Luas (Ha)', 'urea' => 'Urea', 'sp36' => 'SP36', 'za' => 'ZA', 'npk' => 'NPK', 'organik' => 'Organik'];
@endphp

    <h1>LEMBAR KERJA — INPUT RDKK PUPUK</h1>
    <p class="sub">Pengajuan #{{ $suratPupuk->id }} — dicetak {{ now()->translatedFormat('d F Y, H:i') }}</p>

    <div class="section">A. Data Kelompok</div>
    <table class="info">
        <tr><td class="l">Nama Penyuluh</td><td>{{ $suratPupuk->nama_penyuluh }}</td><td class="l">Kode Desa</td><td>{{ $suratPupuk->kode_desa ?: '-' }}</td></tr>
        <tr><td class="l">Kode Kios Pengecer</td><td>{{ $suratPupuk->kode_kios ?: '-' }}</td><td class="l">Nama Kios Pengecer</td><td>{{ $suratPupuk->nama_kios ?: '-' }}</td></tr>
        <tr><td class="l">Gapoktan</td><td>{{ $suratPupuk->gapoktan ?: '-' }}</td><td class="l">Nama Poktan</td><td>{{ $suratPupuk->poktan->nama_kelompok }}</td></tr>
    </table>

    <div class="section">B. Biodata Petani</div>
    <table>
        <thead>
            <tr><th>No</th><th>Nama Petani</th><th>NIK (KTP)</th><th>Tempat Lahir</th><th>Tgl Lahir</th><th>Nama Ibu Kandung</th><th>Alamat</th><th>Subsektor</th></tr>
        </thead>
        <tbody>
            @foreach ($suratPupuk->items as $i => $it)
                <tr>
                    <td>{{ $i + 1 }}</td><td>{{ $it->nama_petani }}</td><td>{{ $it->nik }}</td>
                    <td>{{ $it->tempat_lahir }}</td><td>{{ $it->tanggal_lahir->format('Y-m-d') }}</td>
                    <td>{{ $it->nama_ibu_kandung }}</td><td>{{ $it->alamat }}</td><td>{{ $it->subsektor }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach ([0, 1, 2] as $m)
        <div class="section">C{{ $m + 1 }}. Musim Tanam {{ $m + 1 }} (pupuk dalam kg)</div>
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Nama Petani</th><th>Komoditas</th>
                    @foreach ($pupuk as $label) <th class="r">{{ $label }}</th> @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($suratPupuk->items as $i => $it)
                    @php $row = $it->musim[$m] ?? []; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td><td>{{ $it->nama_petani }}</td><td>{{ $row['komoditas'] ?? '-' }}</td>
                        @foreach (array_keys($pupuk) as $k) <td class="r">{{ $fmt($row[$k] ?? 0) }}</td> @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><b>Total</b></td>
                    @foreach (array_keys($pupuk) as $k) <td class="r"><b>{{ $fmt($rekap[$m][$k]) }}</b></td> @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach

    <p style="margin-top:10px;color:#5C6B62;">Catatan: komoditas "-" berarti tidak diusahakan pada musim tanam tersebut.</p>
</body>
</html>