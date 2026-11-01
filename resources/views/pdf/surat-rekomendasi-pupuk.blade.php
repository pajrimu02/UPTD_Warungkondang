<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 1cm 1.2cm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; margin: 0; }
        h1 { font-size: 13px; text-align: center; margin: 0 0 10px; }
        table.info { margin: 0 0 8px; }
        table.info td { padding: 1px 6px 1px 0; vertical-align: top; font-size: 10px; }
        table.info td.l { width: 70px; }
        table.rdkk { width: 100%; border-collapse: collapse; }
        table.rdkk th, table.rdkk td { border: 1px solid #000; padding: 2px; }
        table.rdkk th { text-align: center; font-weight: bold; vertical-align: middle; font-size: 8px; }
        table.rdkk td { font-size: 9px; }
        table.rdkk td.c { text-align: center; }
        table.rdkk td.r { text-align: right; }
        table.rdkk tr { page-break-inside: avoid; }
        .kecil { font-size: 7px; color: #333; }
        table.ttd { width: 100%; margin-top: 22px; page-break-inside: avoid; }
        table.ttd td { text-align: center; vertical-align: top; font-size: 10px; }
        .pb { page-break-before: always; }
    </style>
</head>
<body>
@php
    $s = $suratPupuk;
    $items = $s->items;

    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',') ?: '0';

    $pupuk = ['urea' => 'UREA', 'sp36' => 'SP-36', 'za' => 'ZA', 'npk' => 'NPK', 'organik' => 'ORGANIK'];

    // Nilai satu kolom: $mt = 0, 1, 2 (MT I, II, III)
    $nilai = fn ($it, $k, $mt) => (float) data_get(collect($it->musim)->values()->get($mt), $k, 0);

    // Luas tanam satu petani: lahan yang sama ditanam tiap musim, jadi diambil nilai terbesar (bukan dijumlah)
    $luasPetani = fn ($it) => (float) (collect($it->musim)->map(fn ($m) => (float) data_get($m, 'luas', 0))->max() ?? 0);

    $desaTeks = ($s->poktan->desa?->nama ?? '-') . ($s->kode_desa ? ' (' . $s->kode_desa . ')' : '');

    // Sub sektor: tampilkan yang dipilih di form (kalau campuran, dipisah koma)
    $subsektorTeks = $items->pluck('subsektor')->filter()->unique()->implode(', ') ?: '-';
@endphp

@foreach (['ringkas', 'rinci'] as $mode)

    @php
        $baris = $mode === 'ringkas'
            ? collect([[
                'utama' => $s->poktan->nama_kelompok ?: '-',
                'kecil' => $s->gapoktan ? 'Gapoktan ' . $s->gapoktan : null,
                'items' => $items,
            ]])
            : $items->values()->map(fn ($it) => [
                'utama' => $it->nama_petani,
                'kecil' => 'NIK ' . $it->nik,
                'items' => collect([$it]),
            ]);
    @endphp

    <div class="{{ $mode === 'rinci' ? 'pb' : '' }}">

        @if ($mode === 'ringkas')
            <h1>E-RDKK PUPUK BERSUBSIDI</h1>
            <table class="info">
                <tr><td class="l">Desa</td><td>= {{ $desaTeks }}</td></tr>
                <tr><td class="l">Sub Sektor</td><td>= {{ $subsektorTeks }}</td></tr>
            </table>
        @else
            <h1>LAMPIRAN: RINCIAN PER PETANI</h1>
            <table class="info">
                <tr><td class="l">Ketua Poktan</td><td>= {{ $s->poktan->nama_ketua ?: '-' }}</td></tr>
                <tr><td class="l">Desa</td><td>= {{ $desaTeks }}</td></tr>
                <tr><td class="l">Poktan</td><td>= {{ $s->poktan->nama_kelompok }}</td></tr>
                <tr><td class="l">Kios</td><td>= {{ trim(($s->kode_kios ? $s->kode_kios . ' - ' : '') . $s->nama_kios) ?: '-' }}</td></tr>
                <tr><td class="l">Penyuluh</td><td>= {{ $s->nama_penyuluh ?: '-' }}</td></tr>
            </table>
        @endif

        <table class="rdkk">
            <thead>
                <tr>
                    <th rowspan="2" style="width:3%;">No</th>
                    <th rowspan="2" style="width:14%;">{{ $mode === 'ringkas' ? 'Poktan' : 'Nama Petani' }}</th>
                    <th rowspan="2" style="width:6%;">Luas<br>Tanam<br>(Ha)</th>
                    @foreach ($pupuk as $label)
                        <th colspan="4">{{ $label }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($pupuk as $label)
                        <th style="width:3.4%;">MT<br>I</th>
                        <th style="width:3.4%;">MT<br>II</th>
                        <th style="width:3.4%;">MT<br>III</th>
                        <th style="width:4.8%;">Jml</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($baris as $n => $b)
                    @php $grp = $b['items']; @endphp
                    <tr>
                        <td class="c">{{ $n + 1 }}</td>
                        <td>
                            {{ $b['utama'] }}
                            @if ($b['kecil'])<br><span class="kecil">{{ $b['kecil'] }}</span>@endif
                        </td>
                        <td class="r">{{ $fmt($grp->sum($luasPetani)) }}</td>
                        @foreach ($pupuk as $k => $label)
                            @php $jml = 0; @endphp
                            @foreach ([0, 1, 2] as $mt)
                                @php $v = $grp->sum(fn ($it) => $nilai($it, $k, $mt)); $jml += $v; @endphp
                                <td class="r">{{ $v ? $fmt($v) : '-' }}</td>
                            @endforeach
                            <td class="r"><b>{{ $jml ? $fmt($jml) : '-' }}</b></td>
                        @endforeach
                    </tr>
                @endforeach

                {{-- Baris Total --}}
                <tr>
                    <td colspan="2" class="c"><b>Total</b></td>
                    <td class="r"><b>{{ $fmt($items->sum($luasPetani)) }}</b></td>
                    @foreach ($pupuk as $k => $label)
                        @php $jml = 0; @endphp
                        @foreach ([0, 1, 2] as $mt)
                            @php $v = $items->sum(fn ($it) => $nilai($it, $k, $mt)); $jml += $v; @endphp
                            <td class="r"><b>{{ $v ? $fmt($v) : '-' }}</b></td>
                        @endforeach
                        <td class="r"><b>{{ $jml ? $fmt($jml) : '-' }}</b></td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        @if ($mode === 'ringkas')
            <table class="ttd">
                <tr>
                    <td style="width:40%;">
                        Menyetujui,<br>
                        Kepala Desa
                        <br><br><br><br><br>
                        ..............................
                    </td>
                    <td style="width:20%;"></td>
                    <td style="width:40%;">
                        {{ config('uptd.tempat') }}, {{ ($s->disetujui_at ?? now())->translatedFormat('d F Y') }}<br>
                        {{ config('uptd.jabatan_penandatangan') }}
                        <br><br><br><br><br>
                        <b><u>{{ config('uptd.nama_penandatangan') }}</u></b><br>
                        NIP. {{ config('uptd.nip_penandatangan') }}
                    </td>
                </tr>
            </table>
        @endif
    </div>

@endforeach
</body>
</html>