@extends('layouts.user')

@section('title', 'Poktan')

@section('content')

    @php
        $badge = [
            'draft' => ['Draft', 'bg-slate-100 text-slate-700'],
            'diajukan' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-800'],
            'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'ditolak' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Poktan</h1>
        <p class="text-sm text-[#5C6B62]">{{ $poktan ? 'Kelompok tani Anda dan status pengajuannya.' : 'Pilih sesuai peran Anda di kelompok tani.' }}</p>
    </div>

    @if ($poktan)
        @php [$labelStatus, $kelasStatus] = $badge[$poktan->status] ?? ['-', 'bg-slate-100 text-slate-700']; @endphp

        <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6 md:p-7">
            <div class="flex items-start gap-4">
                <span class="w-14 h-14 shrink-0 rounded-2xl bg-[#1B4332] text-[#E9C85A] flex items-center justify-center text-2xl">
                    <i class="bi bi-people-fill"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <h2 class="font-serif text-xl text-[#1B4332] truncate">{{ $poktan->nama_kelompok }}</h2>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $kelasStatus }}">{{ $labelStatus }}</span>
                    </div>
                    <p class="text-sm text-[#5C6B62] mt-1">
                        <i class="bi bi-geo-alt"></i> {{ $poktan->desa->nama ?? '-' }}
                        · <span class="font-medium text-[#1B4332]">Anda: {{ $isKetua ? 'Ketua' : 'Anggota' }}</span>
                    </p>
                </div>
            </div>

            <dl class="grid grid-cols-3 gap-4 mt-6 pt-5 border-t border-[#E1DCC9] text-sm">
                <div><dt class="text-xs text-[#8C9086]">Ketua</dt><dd class="font-medium mt-0.5 truncate">{{ $poktan->nama_ketua ?: ($poktan->pengaju->name ?? '-') }}</dd></div>
                <div><dt class="text-xs text-[#8C9086]">Anggota</dt><dd class="font-medium mt-0.5">{{ $poktan->anggota_count }} / {{ $maksAnggota }} orang</dd></div>
                <div><dt class="text-xs text-[#8C9086]">Luas Lahan</dt><dd class="font-medium mt-0.5">{{ $fmt($poktan->luas_total ?? 0) }} ha</dd></div>
            </dl>

            <div class="mt-5">
                @if ($poktan->status === 'draft')
                    @if ($isKetua)
                        <a href="{{ route('user.poktan.anggota', $poktan) }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-5 py-2.5 rounded-full">
                            Kelola anggota & kode undangan <i class="bi bi-arrow-right"></i>
                        </a>
                    @elseif ($saya && $saya->status === 'lengkap')
                        <p class="text-sm bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">
                            <i class="bi bi-check-circle-fill"></i> Data Anda sudah lengkap. Menunggu ketua mengajukan poktan.
                            <a href="{{ route('user.poktan.isi', $poktan) }}" class="font-semibold underline underline-offset-2 ml-1">Ubah data</a>
                        </p>
                    @elseif ($saya)
                        <a href="{{ route('user.poktan.isi', $poktan) }}" class="inline-flex items-center gap-2 bg-[#C9A227] text-[#1B4332] text-sm font-semibold px-5 py-2.5 rounded-full">
                            Lengkapi data diri Anda <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                @elseif ($poktan->status === 'diajukan')
                    <p class="text-sm bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3"><i class="bi bi-hourglass-split"></i> Pengajuan sedang diverifikasi admin UPTD.</p>
                @elseif ($poktan->status === 'disetujui')
                    <div class="grid sm:grid-cols-3 gap-3 pt-1">
                        <a href="{{ route('user.poktan.show', $poktan) }}#detail-poktan"
                           class="group border border-[#E1DCC9] bg-[#FAF9F4] hover:border-[#2D6A4F] hover:bg-white rounded-2xl p-4 transition">
                            <span class="w-9 h-9 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-base mb-3">
                                <i class="bi bi-info-circle"></i>
                            </span>
                            <p class="font-semibold text-sm text-[#1B4332] group-hover:text-[#2D6A4F]">Detail Poktan</p>
                            <p class="text-xs text-[#5C6B62] mt-0.5">Sekretariat & ringkasan lahan</p>
                        </a>

                        <a href="{{ route('user.poktan.sk-resmi', $poktan) }}"
                           class="group border border-[#E1DCC9] bg-[#FAF9F4] hover:border-[#2D6A4F] hover:bg-white rounded-2xl p-4 transition">
                            <span class="w-9 h-9 rounded-xl bg-[#F6EDC9] text-[#7A5C12] flex items-center justify-center text-base mb-3">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                            <p class="font-semibold text-sm text-[#1B4332] group-hover:text-[#2D6A4F]">Surat Resmi (SK)</p>
                            <p class="text-xs text-[#5C6B62] mt-0.5">
                                {{ $poktan->diserahkan_at && $poktan->file_sk_resmi ? 'SK resmi siap dilihat & diunduh' : 'Cek status penerbitan SK' }}
                            </p>
                        </a>

                        <a href="{{ route('user.poktan.show', $poktan) }}#daftar-anggota"
                           class="group border border-[#E1DCC9] bg-[#FAF9F4] hover:border-[#2D6A4F] hover:bg-white rounded-2xl p-4 transition">
                            <span class="w-9 h-9 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-base mb-3">
                                <i class="bi bi-people"></i>
                            </span>
                            <p class="font-semibold text-sm text-[#1B4332] group-hover:text-[#2D6A4F]">Daftar Anggota</p>
                            <p class="text-xs text-[#5C6B62] mt-0.5">{{ $poktan->anggota_count }} anggota & berkas Anda</p>
                        </a>
                    </div>
                @else
                    <div class="text-sm bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
                        <p class="font-semibold">Pengajuan ditolak</p>
                        <p class="mt-1">{{ $poktan->catatan_admin ?? 'Tidak ada catatan dari admin.' }}</p>
                    </div>
                    @if ($isKetua)
                        <form method="POST" action="{{ route('user.poktan.perbaiki', $poktan) }}" class="mt-3"
                              data-judul="Buka kembali pengajuan?"
                              data-konfirmasi="Pengajuan kembali berstatus draft agar bisa diperbaiki, lalu diajukan ulang."
                              data-tombol="Ya, Perbaiki" data-tipe="normal">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-5 py-2.5 rounded-full">
                                <i class="bi bi-arrow-repeat"></i> Perbaiki & Ajukan Ulang
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-5">

            <a href="{{ route('user.poktan.gabung') }}" class="group block bg-white border border-[#E1DCC9] rounded-3xl p-7 hover:border-[#2D6A4F] hover:shadow-lg transition">
                <span class="w-14 h-14 rounded-2xl bg-[#EAF2EC] text-[#1B4332] group-hover:bg-[#1B4332] group-hover:text-[#E9C85A] flex items-center justify-center text-2xl transition-colors">
                    <i class="bi bi-key"></i>
                </span>
                <p class="text-xs font-semibold tracking-wide text-[#2D6A4F] mt-5">SAYA ANGGOTA</p>
                <h2 class="font-serif text-xl text-[#1B4332] mt-1 leading-snug">Gabung ke poktan yang sudah dibuat</h2>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Ketua Anda sudah membuat poktan dan memberi <b>kode undangan</b> 6 karakter, atau <b>tautan</b> yang bisa langsung dibuka.
                </p>
                <ol class="mt-4 space-y-1.5 text-sm text-[#5C6B62]">
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">1.</span> Masukkan kode</li>
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">2.</span> Konfirmasi poktan</li>
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">3.</span> Isi data dan unggah berkas sendiri</li>
                </ol>
                <span class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-[#1B4332]">
                    Masukkan Kode <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                </span>
            </a>

            <a href="{{ route('user.poktan.create') }}" class="group block bg-white border border-[#E1DCC9] rounded-3xl p-7 hover:border-[#2D6A4F] hover:shadow-lg transition">
                <span class="w-14 h-14 rounded-2xl bg-[#F6EDC9] text-[#7A5C12] group-hover:bg-[#1B4332] group-hover:text-[#E9C85A] flex items-center justify-center text-2xl transition-colors">
                    <i class="bi bi-person-badge"></i>
                </span>
                <p class="text-xs font-semibold tracking-wide text-[#7A5C12] mt-5">SAYA KETUA</p>
                <h2 class="font-serif text-xl text-[#1B4332] mt-1 leading-snug">Buat poktan baru</h2>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Poktan belum ada. Anda yang membuatnya, otomatis menjadi ketua, lalu membagikan kode atau tautan undangan ke anggota.
                </p>
                <ol class="mt-4 space-y-1.5 text-sm text-[#5C6B62]">
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">1.</span> Isi nama poktan, desa, alamat</li>
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">2.</span> Bagikan kode atau tautan</li>
                    <li class="flex gap-2"><span class="font-semibold text-[#1B4332]">3.</span> Ajukan setelah semua anggota lengkap</li>
                </ol>
                <span class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-[#1B4332]">
                    Ajukan Poktan <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                </span>
            </a>
        </div>

        <p class="text-xs text-[#8C9086] mt-4">
            <i class="bi bi-link-45deg"></i> Punya tautan undangan? Cukup buka tautannya di browser ini. Setelah masuk, Anda langsung diarahkan untuk bergabung.
        </p>

        <div class="grid lg:grid-cols-5 gap-5 mt-8">
            <div class="lg:col-span-3 bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <p class="text-xs font-semibold tracking-wide text-[#2D6A4F]">PERSYARATAN PEMBENTUKAN POKTAN</p>
                <ul class="mt-4 space-y-4">
                    @foreach ([
                        ['bi-person-vcard', 'KTP', 'Setiap anggota mengunggah KTP-nya sendiri.'],
                        ['bi-people', 'Kartu Keluarga (KK)', 'Setiap anggota mengunggah KK dan mengisi nomor KK.'],
                        ['bi-map', 'SPPT Sawah', 'Bukti kepemilikan sawah beserta luasnya. Poktan hanya dapat terbentuk jika SPPT sawah dapat dibuktikan.'],
                    ] as [$icon, $judul, $isi])
                        <li class="flex gap-3.5 items-start">
                            <span class="w-10 h-10 shrink-0 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center"><i class="bi {{ $icon }}"></i></span>
                            <div>
                                <p class="font-semibold text-sm text-[#14231C]">{{ $judul }}</p>
                                <p class="text-sm text-[#5C6B62] mt-0.5 leading-relaxed">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-5 text-sm bg-amber-50 border border-amber-200 text-amber-900 rounded-xl px-4 py-3 leading-relaxed">
                    <i class="bi bi-exclamation-triangle"></i>
                    Total luas lahan seluruh anggota minimal <b>{{ $fmt($minLuas) }} ha</b>, dengan anggota maksimal <b>{{ $maksAnggota }} orang</b>. Berkas tidak boleh diwakilkan ketua.
                </p>
                <p class="mt-3 text-xs text-[#8C9086]">Format berkas JPG, PNG, atau PDF, maksimal 2 MB per berkas.</p>
            </div>

            <div class="lg:col-span-2 bg-[#1B4332] text-white rounded-2xl p-6">
                <p class="text-xs font-semibold tracking-wide text-[#E9C85A]">ALUR PENGAJUAN</p>
                <ol class="mt-4 space-y-4">
                    @foreach ([
                        'Ketua membuat poktan dan mendapat kode serta tautan undangan',
                        'Ketua membagikan kode atau tautan ke calon anggota',
                        'Tiap anggota masuk atau mendaftar akun, bergabung, lalu mengisi data dan berkasnya sendiri',
                        'Ketua mengajukan setelah semua anggota lengkap',
                        'Admin UPTD memverifikasi, lalu poktan aktif',
                    ] as $n => $teks)
                        <li class="flex gap-3 items-start">
                            <span class="w-7 h-7 shrink-0 rounded-full bg-[#C9A227] text-[#1B4332] text-xs font-bold flex items-center justify-center">{{ $n + 1 }}</span>
                            <span class="text-sm text-[#E4EAE1] leading-relaxed">{{ $teks }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @endif

@endsection