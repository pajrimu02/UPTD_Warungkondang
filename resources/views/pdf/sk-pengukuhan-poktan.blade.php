<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1.8cm 2.2cm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; line-height: 1.5; color: #000; margin: 0; }
        .draf { text-align: center; font-size: 10px; color: #B00020; border: 1px solid #B00020; padding: 3px; margin-bottom: 10px; }
        .judul { text-align: center; font-weight: bold; margin: 0; }
        .judul-besar { font-size: 14px; }
        .nomor { text-align: center; margin: 2px 0 10px; }
        p { margin: 0 0 6px; text-align: justify; }
        table.kv td { vertical-align: top; padding: 1px 4px 1px 0; }
        table.kv td.l { width: 105px; }
        table.isi { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.isi td { vertical-align: top; padding: 1px 0; }
        table.isi td.l { width: 90px; }
        table.isi td.n { width: 22px; }
        table.tabel { width: 100%; border-collapse: collapse; margin: 6px 0; }
        table.tabel th, table.tabel td { border: 1px solid #000; padding: 3px 5px; font-size: 11px; }
        table.tabel th { text-align: center; }
        table.tabel tr { page-break-inside: avoid; }
        table.ttd { width: 100%; margin-top: 14px; page-break-inside: avoid; }
        table.ttd td.kanan { width: 250px; text-align: center; vertical-align: top; }
        .lampiran { page-break-before: always; }
    </style>
</head>
<body>
@php
    $p = $poktan;
    $urut = ['ketua' => 1, 'sekretaris' => 2, 'bendahara' => 3, 'anggota' => 4];
    $aktif = $p->anggota->where('status', '!=', 'nonaktif')
        ->sortBy(fn ($a) => ($urut[$a->jabatan] ?? 9) . '|' . $a->nama)->values();
    $pengurus = $aktif->whereIn('jabatan', ['ketua', 'sekretaris', 'bendahara'])->values();
    $jumlah = $aktif->count();
    $luas = $aktif->sum('luas_lahan_ha');
    $fmt = fn ($n) => number_format((float) $n, 2, ',', '.');

    $nomor = $p->getAttribute('nomor_sk') ?: '___/SK-POKTAN/UPTD/___/____';
    $tanggal = $p->getAttribute('tanggal_sk') ?: ($p->disetujui_at ?: now());
    $namaPoktan = strtoupper($p->nama_kelompok);
    $desa = $p->desa?->nama ?? '-';
    $kec = config('uptd.kecamatan');
    $kab = config('uptd.kabupaten');
    $ketua = $aktif->firstWhere('jabatan', 'ketua');
@endphp

    @if ($draf ?? false)
        <div class="draf">DRAF SEMENTARA: format dan dasar hukum menunggu format resmi UPTD. Bukan dokumen resmi.</div>
    @endif

    <p class="judul judul-besar">KEPUTUSAN KEPALA {{ strtoupper(config('uptd.nama')) }}</p>
    <p class="nomor">NOMOR: {{ $nomor }}</p>
    <p class="judul">TENTANG</p>
    <p class="judul">PENGUKUHAN KELOMPOK TANI {{ $namaPoktan }}</p>
    <p class="judul">DESA {{ strtoupper($desa) }} KECAMATAN {{ strtoupper($kec) }} KABUPATEN {{ strtoupper($kab) }}</p>
    <p class="judul" style="margin:8px 0;">KEPALA {{ strtoupper(config('uptd.nama')) }},</p>

    <table class="isi">
        <tr>
            <td class="l">Menimbang</td><td class="n">:</td>
            <td>
                <table width="100%">
                    <tr><td width="20" valign="top">a.</td><td><p>bahwa untuk meningkatkan kemampuan petani dan memudahkan pembinaan serta pelayanan, perlu dibentuk kelompok tani yang dikukuhkan secara resmi;</p></td></tr>
                    <tr><td valign="top">b.</td><td><p>bahwa Kelompok Tani {{ $p->nama_kelompok }} di Desa {{ $desa }} telah mengajukan pengukuhan dan berdasarkan hasil verifikasi berkas anggota dan luas lahan telah memenuhi persyaratan, dengan jumlah anggota {{ $jumlah }} orang dan total luas lahan sawah {{ $fmt($luas) }} hektare;</p></td></tr>
                    <tr><td valign="top">c.</td><td><p>bahwa berdasarkan pertimbangan sebagaimana dimaksud dalam huruf a dan huruf b, perlu menetapkan Keputusan Kepala {{ config('uptd.nama') }} tentang Pengukuhan Kelompok Tani {{ $p->nama_kelompok }};</p></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="l">Mengingat</td><td class="n">:</td>
            <td>
                <table width="100%">
                    <tr><td width="20" valign="top">1.</td><td><p>Peraturan Menteri Pertanian Nomor 67/Permentan/SM.050/12/2016 tentang Pembinaan Kelembagaan Petani;</p></td></tr>
                    <tr><td valign="top">2.</td><td><p>[DASAR HUKUM DAERAH: isi sesuai format resmi UPTD];</p></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="judul" style="margin:6px 0;">MEMUTUSKAN:</p>

    <table class="isi">
        <tr><td class="l">Menetapkan</td><td class="n">:</td><td><p><b>KEPUTUSAN KEPALA {{ strtoupper(config('uptd.nama')) }} TENTANG PENGUKUHAN KELOMPOK TANI {{ $namaPoktan }}.</b></p></td></tr>
        <tr><td class="l">KESATU</td><td class="n">:</td>
            <td>
                <p>Mengukuhkan Kelompok Tani {{ $p->nama_kelompok }} dengan data sebagai berikut:</p>
                <table class="kv">
                    <tr><td class="l">Nama Kelompok</td><td>: {{ $p->nama_kelompok }}</td></tr>
                    <tr><td class="l">Ketua</td><td>: {{ $ketua?->nama ?? $p->nama_ketua ?? '-' }}</td></tr>
                    <tr><td class="l">Alamat</td><td>: {{ $p->alamat ?: '-' }}</td></tr>
                    <tr><td class="l">Desa</td><td>: {{ $desa }}, Kecamatan {{ $kec }}, Kabupaten {{ $kab }}</td></tr>
                    <tr><td class="l">Jumlah Anggota</td><td>: {{ $jumlah }} orang</td></tr>
                    <tr><td class="l">Total Luas Lahan</td><td>: {{ $fmt($luas) }} hektare</td></tr>
                </table>
            </td>
        </tr>
        <tr><td class="l">KEDUA</td><td class="n">:</td><td><p>Susunan pengurus dan daftar anggota Kelompok Tani sebagaimana dimaksud dalam Diktum KESATU tercantum dalam Lampiran yang merupakan bagian tidak terpisahkan dari Keputusan ini.</p></td></tr>
        <tr><td class="l">KETIGA</td><td class="n">:</td><td><p>Kelompok Tani sebagaimana dimaksud dalam Diktum KESATU memperoleh pembinaan dan pelayanan sesuai ketentuan peraturan perundang-undangan. [RINCIAN HAK DAN KEWAJIBAN: sesuaikan format resmi].</p></td></tr>
        <tr><td class="l">KEEMPAT</td><td class="n">:</td><td><p>Ketua Kelompok Tani wajib melaporkan setiap perubahan pengurus dan anggota serta memperbarui data kelompok kepada {{ config('uptd.nama') }}.</p></td></tr>
        <tr><td class="l">KELIMA</td><td class="n">:</td><td><p>Keputusan ini mulai berlaku pada tanggal ditetapkan. Apabila di kemudian hari terdapat kekeliruan, akan diadakan perbaikan sebagaimana mestinya.</p></td></tr>
    </table>

    <table class="ttd">
        <tr>
            <td></td>
            <td class="kanan">
                <p style="text-align:left;">Ditetapkan di {{ config('uptd.tempat') }}<br>pada tanggal {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
                <p style="text-align:center; margin-top:6px;">{{ strtoupper(config('uptd.sk_jabatan_penandatangan')) }},</p>
                <br><br><br><br>
                <p style="text-align:center;"><b><u>{{ config('uptd.sk_nama_penandatangan') }}</u></b><br>NIP. {{ config('uptd.sk_nip_penandatangan') }}</p>
            </td>
        </tr>
    </table>

    {{-- ============ LAMPIRAN ============ --}}
    <div class="lampiran">
        <p style="margin:0;">Lampiran Keputusan Kepala {{ config('uptd.nama') }}</p>
        <p style="margin:0;">Nomor&nbsp;&nbsp;&nbsp;: {{ $nomor }}</p>
        <p style="margin:0 0 10px;">Tentang : Pengukuhan Kelompok Tani {{ $p->nama_kelompok }}</p>

        <p class="judul" style="margin-bottom:4px;">A. SUSUNAN PENGURUS</p>
        <table class="tabel">
            <thead><tr><th width="30">No</th><th>Nama</th><th width="120">Jabatan</th></tr></thead>
            <tbody>
                @forelse ($pengurus as $i => $a)
                    <tr><td align="center">{{ $i + 1 }}</td><td>{{ $a->nama }}</td><td>{{ ucfirst($a->jabatan) }}</td></tr>
                @empty
                    <tr><td colspan="3" align="center">-</td></tr>
                @endforelse
            </tbody>
        </table>

        <p class="judul" style="margin:10px 0 4px;">B. DAFTAR ANGGOTA</p>
        <table class="tabel">
            <thead><tr><th width="30">No</th><th>Nama</th><th width="110">Jabatan</th><th width="100">Luas Lahan (ha)</th></tr></thead>
            <tbody>
                @foreach ($aktif as $i => $a)
                    <tr>
                        <td align="center">{{ $i + 1 }}</td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ ucfirst($a->jabatan) }}</td>
                        <td align="right">{{ $fmt($a->luas_lahan_ha) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><b>Total ({{ $jumlah }} anggota)</b></td>
                    <td align="right"><b>{{ $fmt($luas) }}</b></td>
                </tr>
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td></td>
                <td class="kanan">
                    <p style="text-align:center;">{{ strtoupper(config('uptd.sk_jabatan_penandatangan')) }},</p>
                    <br><br><br>
                    <p style="text-align:center;"><b><u>{{ config('uptd.sk_nama_penandatangan') }}</u></b><br>NIP. {{ config('uptd.sk_nip_penandatangan') }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>