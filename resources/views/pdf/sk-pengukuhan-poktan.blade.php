<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1.8cm 2.2cm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; line-height: 1.5; color: #000; margin: 0; }
        .draf { text-align: center; font-size: 10px; color: #B00020; border: 1px solid #B00020; padding: 3px; margin-bottom: 10px; }
        .kop { border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px; text-align: center; }
        .kop p { margin: 0; text-align: center; line-height: 1.25; }
        .judul { text-align: center; font-weight: bold; margin: 0; }
        .judul-besar { font-size: 13px; }
        .nomor { text-align: center; margin: 2px 0 10px; }
        p { margin: 0 0 6px; text-align: justify; }
        table.kv td { vertical-align: top; padding: 1px 4px 1px 0; }
        table.kv td.l { width: 135px; }
        table.isi { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.isi td { vertical-align: top; padding: 1px 0; }
        table.isi td.l { width: 95px; }
        table.isi td.n { width: 20px; }
        table.tabel { width: 100%; border-collapse: collapse; margin: 6px 0; }
        table.tabel th, table.tabel td { border: 1px solid #000; padding: 4px 6px; font-size: 10.5px; }
        table.tabel th { text-align: center; background-color: #f4f4f4; }
        table.tabel tr { page-break-inside: avoid; }
        table.ttd { width: 100%; margin-top: 14px; page-break-inside: avoid; }
        table.ttd td.kanan { width: 260px; text-align: center; vertical-align: top; }
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

    $nomor = $p->getAttribute('nomor_sk') ?: '500.6.1/___/UPTD-WRK/___/____';
    $tanggal = $p->getAttribute('tanggal_sk') ?: ($p->disetujui_at ?: now());
    $namaPoktan = strtoupper($p->nama_kelompok);
    $desa = $p->desa?->nama ?? '-';
    $kodeDesa = $p->desa?->kode_desa ?? '-';
    $kec = config('uptd.kecamatan');
    $kab = config('uptd.kabupaten');
    $ketua = $aktif->firstWhere('jabatan', 'ketua');
@endphp

    @if ($draf ?? false)
        <div class="draf">PRATINJAU DOKUMEN (DRAF) — Belum Disahkan / Belum Ditandatangani Kepala UPTD.</div>
    @endif

    <div class="kop">
        <p style="font-size: 13px; font-weight: bold;">PEMERINTAH KABUPATEN {{ strtoupper($kab) }}</p>
        <p style="font-size: 13px; font-weight: bold;">DINAS TANAMAN PANGAN, HORTIKULTURA, PERKEBUNAN DAN KETAHANAN PANGAN</p>
        <p style="font-size: 13px; font-weight: bold;">{{ strtoupper(config('uptd.nama')) }}</p>
        <p style="font-size: 10px;">Kecamatan {{ $kec }}, Kabupaten {{ $kab }}, Provinsi Jawa Barat</p>
    </div>

    <p class="judul judul-besar">KEPUTUSAN KEPALA {{ strtoupper(config('uptd.nama')) }}</p>
    <p class="nomor">NOMOR : {{ $nomor }}</p>
    <p class="judul">TENTANG</p>
    <p class="judul">PENGUKUHAN KELOMPOK TANI "{{ $namaPoktan }}"</p>
    <p class="judul">DESA {{ strtoupper($desa) }} KECAMATAN {{ strtoupper($kec) }} KABUPATEN {{ strtoupper($kab) }}</p>
    <p class="judul" style="margin:8px 0;">KEPALA {{ strtoupper(config('uptd.nama')) }},</p>

    <table class="isi">
        <tr>
            <td class="l">Menimbang</td><td class="n">:</td>
            <td>
                <table width="100%">
                    <tr><td width="20" valign="top">a.</td><td><p>bahwa dalam rangka meningkatkan kemampuan, kemandirian, serta efektivitas pembinaan dan fasilitasi sarana produksi pertanian kepada petani, perlu dibentuk kelembagaan kelompok tani yang dikukuhkan secara resmi;</p></td></tr>
                    <tr><td valign="top">b.</td><td><p>bahwa Kelompok Tani "{{ $p->nama_kelompok }}" yang berkedudukan di Desa {{ $desa }}, Kecamatan {{ $kec }} telah mengajukan permohonan pengukuhan dan berdasarkan hasil verifikasi administrasi serta luas lahan garapan telah memenuhi persyaratan dengan jumlah anggota {{ $jumlah }} orang dan total luas lahan {{ $fmt($luas) }} hektare;</p></td></tr>
                    <tr><td valign="top">c.</td><td><p>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan huruf b, perlu menetapkan Keputusan Kepala {{ config('uptd.nama') }} tentang Pengukuhan Kelompok Tani "{{ $p->nama_kelompok }}";</p></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="l">Mengingat</td><td class="n">:</td>
            <td>
                <table width="100%">
                    <tr><td width="20" valign="top">1.</td><td><p>Undang-Undang Nomor 19 Tahun 2013 tentang Perlindungan dan Pemberdayaan Petani (Lembaran Negara Republik Indonesia Tahun 2013 Nomor 131, Tambahan Lembaran Negara Republik Indonesia Nomor 5433);</p></td></tr>
                    <tr><td valign="top">2.</td><td><p>Peraturan Menteri Pertanian Nomor 67/Permentan/SM.050/12/2016 tentang Pembinaan Kelembagaan Petani (Berita Negara Republik Indonesia Tahun 2016 Nomor 2038);</p></td></tr>
                    <tr><td valign="top">3.</td><td><p>Peraturan Menteri Pertanian Nomor 03/Permentan/SM.200/1/2018 tentang Pedoman Penyelenggaraan Penyuluhan Pertanian;</p></td></tr>
                    <tr><td valign="top">4.</td><td><p>Peraturan Bupati {{ $kab }} tentang Pembentukan, Kedudukan, Susunan Organisasi, Tugas dan Fungsi serta Tata Kerja Unit Pelaksana Teknis Daerah (UPTD) pada Dinas Pertanian Kabupaten {{ $kab }};</p></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="judul" style="margin:6px 0;">MEMUTUSKAN :</p>

    <table class="isi">
        <tr><td class="l">Menetapkan</td><td class="n">:</td><td><p><b>KEPUTUSAN KEPALA {{ strtoupper(config('uptd.nama')) }} TENTANG PENGUKUHAN KELOMPOK TANI "{{ $namaPoktan }}".</b></p></td></tr>
        <tr><td class="l">KESATU</td><td class="n">:</td>
            <td>
                <p>Mengukuhkan Kelompok Tani "{{ $p->nama_kelompok }}" sebagai kelembagaan petani di wilayah kerja {{ config('uptd.nama') }} dengan identitas sebagai berikut:</p>
                <table class="kv">
                    <tr><td class="l">Nama Kelompok</td><td>: {{ $p->nama_kelompok }}</td></tr>
                    <tr><td class="l">ID / Kode Poktan</td><td>: {{ str_pad((string) $p->id, 4, '0', STR_PAD_LEFT) }} (Registrasi SIMLUHTAN)</td></tr>
                    <tr><td class="l">Ketua Kelompok</td><td>: {{ $ketua?->nama ?? $p->nama_ketua ?? '-' }}</td></tr>
                    <tr><td class="l">Alamat Sekretariat</td><td>: {{ $p->alamat ?: '-' }}</td></tr>
                    <tr><td class="l">Desa / Kode Desa</td><td>: Desa {{ $desa }} ({{ $kodeDesa }}), Kec. {{ $kec }}, Kab. {{ $kab }}</td></tr>
                    <tr><td class="l">Jumlah Anggota</td><td>: {{ $jumlah }} orang</td></tr>
                    <tr><td class="l">Total Luas Lahan</td><td>: {{ $fmt($luas) }} hektare</td></tr>
                    <tr><td class="l">Kelas Kemampuan</td><td>: Kelas Pemula</td></tr>
                </table>
            </td>
        </tr>
        <tr><td class="l">KEDUA</td><td class="n">:</td><td><p>Susunan pengurus dan daftar anggota Kelompok Tani sebagaimana dimaksud dalam Diktum KESATU tercantum dalam Lampiran yang merupakan bagian tidak terpisahkan dari Keputusan ini.</p></td></tr>
        <tr><td class="l">KETIGA</td><td class="n">:</td><td><p>Kelompok Tani sebagaimana dimaksud dalam Diktum KESATU berhak memperoleh pembinaan penyuluhan pertanian, fasilitasi akses sarana produksi pertanian bersubsidi (pupuk dan BBM jenis tertentu), serta pengusulan dalam sistem pendataan penyuluhan pertanian (SIMLUHTAN / e-RDKK) sesuai ketentuan peraturan perundang-undangan.</p></td></tr>
        <tr><td class="l">KEEMPAT</td><td class="n">:</td><td><p>Pengurus dan anggota Kelompok Tani berkewajiban melaksanakan pertemuan rutin kelompok, menyusun Rencana Definitif Kelompok (RDK) dan Rencana Definitif Kebutuhan Kelompok (RDKK), serta melaporkan setiap perubahan susunan pengurus, keanggotaan, maupun luas lahan garapan kepada {{ config('uptd.nama') }}.</p></td></tr>
        <tr><td class="l">KELIMA</td><td class="n">:</td><td><p>Keputusan ini mulai berlaku pada tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan di dalamnya akan diadakan perbaikan sebagaimana mestinya.</p></td></tr>
    </table>

    <table class="ttd">
        <tr>
            <td></td>
            <td class="kanan">
                <p style="text-align:left;">Ditetapkan di : {{ config('uptd.tempat') }}<br>Pada tanggal : {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
                <p style="text-align:center; margin-top:8px;"><b>{{ strtoupper(config('uptd.sk_jabatan_penandatangan')) }},</b></p>
                <br><br><br><br>
                <p style="text-align:center;"><b><u>{{ config('uptd.sk_nama_penandatangan') }}</u></b><br>{{ config('uptd.sk_nip_penandatangan') }}</p>
            </td>
        </tr>
    </table>

    {{-- ============ LAMPIRAN STANDAR SIMLUHTAN ============ --}}
    <div class="lampiran">
        <table style="margin-bottom: 10px;">
            <tr><td width="75">Lampiran</td><td width="12">:</td><td>Keputusan Kepala {{ config('uptd.nama') }}</td></tr>
            <tr><td>Nomor</td><td>:</td><td>{{ $nomor }}</td></tr>
            <tr><td>Tanggal</td><td>:</td><td>{{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</td></tr>
            <tr><td>Tentang</td><td>:</td><td>Pengukuhan Kelompok Tani "{{ $p->nama_kelompok }}" (Desa {{ $desa }})</td></tr>
        </table>

        <p class="judul" style="margin-bottom:4px;">A. SUSUNAN PENGURUS KELOMPOK TANI</p>
        <table class="tabel">
            <thead>
                <tr>
                    <th width="28">No</th>
                    <th>Nama Lengkap</th>
                    <th width="125">NIK</th>
                    <th width="125">Nomor KK</th>
                    <th width="95">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengurus as $i => $a)
                    <tr>
                        <td align="center">{{ $i + 1 }}</td>
                        <td>{{ $a->nama }}</td>
                        <td align="center">{{ $a->nik ?: '-' }}</td>
                        <td align="center">{{ $a->nomor_kk ?: '-' }}</td>
                        <td align="center">{{ ucfirst($a->jabatan) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" align="center">-</td></tr>
                @endforelse
            </tbody>
        </table>

        <p class="judul" style="margin:12px 0 4px;">B. DAFTAR ANGGOTA DAN LUAS LAHAN GARAPAN (DATA SIMLUHTAN)</p>
        <table class="tabel">
            <thead>
                <tr>
                    <th width="26">No</th>
                    <th>Nama Anggota</th>
                    <th width="115">NIK</th>
                    <th width="125">Tempat, Tgl Lahir</th>
                    <th>Alamat</th>
                    <th width="72">Jabatan</th>
                    <th width="75">Luas Lahan (ha)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($aktif as $i => $a)
                    <tr>
                        <td align="center">{{ $i + 1 }}</td>
                        <td>{{ $a->nama }}</td>
                        <td align="center">{{ $a->nik ?: '-' }}</td>
                        <td>{{ $a->tempat_lahir ? $a->tempat_lahir . ', ' . $a->tanggal_lahir?->format('d-m-Y') : '-' }}</td>
                        <td>{{ $a->alamat ?: '-' }}</td>
                        <td align="center">{{ ucfirst($a->jabatan) }}</td>
                        <td align="right">{{ $fmt($a->luas_lahan_ha) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" align="right"><b>Total Keseluruhan ({{ $jumlah }} Anggota)</b></td>
                    <td align="right"><b>{{ $fmt($luas) }}</b></td>
                </tr>
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td></td>
                <td class="kanan">
                    <p style="text-align:center;"><b>{{ strtoupper(config('uptd.sk_jabatan_penandatangan')) }},</b></p>
                    <br><br><br><br>
                    <p style="text-align:center;"><b><u>{{ config('uptd.sk_nama_penandatangan') }}</u></b><br>{{ config('uptd.sk_nip_penandatangan') }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>