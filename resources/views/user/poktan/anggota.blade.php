@extends('layouts.user')

@section('title', 'Kelola Anggota Poktan')

@section('content')

    @php
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif');
        $jumlah = $aktif->count();
        $penuh = $jumlah >= $maksAnggota;
        $total = (float) $aktif->sum('luas_lahan_ha');
        $kurang = max(0, $minLuas - $total);
        $persen = $minLuas > 0 ? min(100, round($total / $minLuas * 100)) : 0;
        $memenuhi = $total >= $minLuas;
        $desa = $poktan->desa;
        $sisaDesa = ($desa && $desa->batasHa() !== null) ? $desa->sisaHa() : null;
        $lebihDesa = $sisaDesa !== null && $total > $sisaDesa;
        $kode = $poktan->kode_undangan;
        $tautan = route('poktan.undangan', $kode);
        $pesanWa = urlencode("Ayo bergabung ke poktan {$poktan->nama_kelompok} di website UPTD Warungkondang. Buka tautan ini lalu masuk atau daftar akun petani: {$tautan} (kode undangan: {$kode})");
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

     <div>
        <x-tombol-kembali :href="route('user.poktan')" />
    </div>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">{{ $poktan->nama_kelompok }}</h1>
    <p class="text-sm text-[#5C6B62] mb-6"><i class="bi bi-geo-alt"></i> {{ $desa->nama ?? '-' }}</p>

    @include('user.poktan._stepper', ['aktif' => 2])

    @if ($poktan->catatan_admin)
        <div class="text-sm bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6">
            <p class="font-semibold">Catatan admin dari penolakan sebelumnya</p>
            <p class="mt-1">{{ $poktan->catatan_admin }}</p>
        </div>
    @endif

    {{-- Kode undangan --}}
    <div class="relative bg-[#1B4332] text-white rounded-3xl p-6 md:p-7 overflow-hidden mb-8">
        <div class="absolute -top-12 -right-12 w-44 h-44 rounded-full bg-[#2D6A4F]/50"></div>
        <div class="relative">
            <p class="text-xs font-semibold tracking-widest text-[#E9C85A]">KODE UNDANGAN</p>
            <div class="flex items-center gap-3 flex-wrap mt-2">
                <p class="font-serif text-4xl tracking-[0.25em]">{{ $kode }}</p>
                <button type="button" data-salin="{{ $kode }}" class="text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/20 rounded-full px-3.5 py-2 transition">
                    <i class="bi bi-clipboard"></i> <span>Salin kode</span>
                </button>
                <button type="button" data-salin="{{ $tautan }}" class="text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/20 rounded-full px-3.5 py-2 transition">
                    <i class="bi bi-link-45deg"></i> <span>Salin tautan</span>
                </button>
                <a href="https://wa.me/?text={{ $pesanWa }}" target="_blank" rel="noopener" class="text-xs font-semibold bg-[#C9A227] text-[#1B4332] rounded-full px-3.5 py-2">
                    <i class="bi bi-whatsapp"></i> Bagikan
                </a>
            </div>
            <p class="text-sm text-[#CFE3D6] mt-4 leading-relaxed max-w-xl">
                Calon anggota harus punya akun sendiri, lalu membuka menu Poktan, memilih Gabung dengan Kode, dan memasukkan kode ini. Kode tidak berlaku lagi setelah poktan diajukan.
            </p>
            <form method="POST" action="{{ route('user.poktan.kode', $poktan) }}" class="mt-3"
                  data-judul="Buat kode baru?"
                  data-konfirmasi="Kode lama langsung tidak berlaku. Anggota yang sudah bergabung tidak terpengaruh."
                  data-tombol="Ya, Buat Kode Baru" data-tipe="normal">
                @csrf
                <button type="submit" class="text-xs text-[#E9C85A] underline underline-offset-2">Kode bocor? Buat kode baru</button>
            </form>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Anggota</p>
            <p class="font-serif text-2xl text-[#1B4332] mt-1">{{ $jumlah }} <span class="text-base text-[#8C9086]">/ {{ $maksAnggota }}</span></p>
            <p class="text-[11px] mt-3 text-[#8C9086]">{{ $penuh ? 'Jumlah anggota sudah penuh' : 'Maksimal ' . $maksAnggota . ' anggota' }}</p>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Total luas lahan</p>
            <p class="font-serif text-2xl {{ $memenuhi ? 'text-[#2D6A4F]' : 'text-[#1B4332]' }} mt-1">{{ $fmt($total) }} ha</p>
            <div class="h-2 rounded-full bg-[#EAF2EC] mt-3 overflow-hidden">
                <div class="h-full rounded-full {{ $memenuhi ? 'bg-[#2D6A4F]' : 'bg-[#C9A227]' }}" style="width: {{ $persen }}%"></div>
            </div>
            <p class="text-[11px] mt-1.5 {{ $memenuhi ? 'text-[#2D6A4F]' : 'text-[#8C9086]' }}">
                {{ $memenuhi ? 'Syarat minimal ' . $fmt($minLuas) . ' ha terpenuhi' : 'Kurang ' . $fmt($kurang) . ' ha lagi (minimal ' . $fmt($minLuas) . ' ha)' }}
            </p>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Sisa batas desa</p>
            @if ($sisaDesa !== null)
                <p class="font-serif text-2xl {{ $lebihDesa ? 'text-red-600' : 'text-[#1B4332]' }} mt-1">{{ $fmt($sisaDesa) }} ha</p>
                <p class="text-[11px] mt-3 {{ $lebihDesa ? 'text-red-600' : 'text-[#8C9086]' }}">
                    {{ $lebihDesa ? 'Total lahan melebihi sisa desa' : ($desa->memakaiBatasBawaan() ? 'Batas sementara desa ' . $desa->nama : 'Luas baku sawah desa ' . $desa->nama) }}
                </p>
            @else
                <p class="font-serif text-2xl text-[#8C9086] mt-1">—</p>
                <p class="text-[11px] mt-3 text-[#8C9086]">Belum ada batas desa</p>
            @endif
        </div>
    </div>

    {{-- Data ketua sendiri --}}
    @if ($saya && $saya->status !== 'lengkap')
        <div class="flex items-center justify-between gap-4 flex-wrap bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6">
            <p class="text-sm text-amber-900"><i class="bi bi-exclamation-circle"></i> Data Anda sebagai ketua belum lengkap.</p>
            <a href="{{ route('user.poktan.isi', $poktan) }}" class="bg-[#C9A227] text-[#1B4332] text-sm font-semibold px-5 py-2 rounded-full">Lengkapi sekarang</a>
        </div>
    @endif

    {{-- Daftar anggota --}}
    <h2 class="font-semibold text-[#14231C] mb-3">Daftar Anggota</h2>
    <div class="space-y-3 mb-8">
        @foreach ($poktan->anggota as $a)
            @php $punyaSaya = (int) $a->user_id === (int) auth()->id(); @endphp
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 md:p-5">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-[#14231C]">{{ $a->nama }}{{ $punyaSaya ? ' (Anda)' : '' }}</p>
                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">{{ $a->jabatan }}</span>
                            @if ($a->status === 'lengkap')
                                <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-emerald-50 text-emerald-700">lengkap</span>
                            @else
                                <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-amber-50 text-amber-700">menunggu data</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#5C6B62] mt-1">
                            NIK {{ $punyaSaya ? $a->nik : substr((string) $a->nik, 0, 6) . '••••••••••' }}
                            @if ($a->luas_lahan_ha) · {{ $fmt($a->luas_lahan_ha) }} ha @endif
                        </p>
                    </div>

                    @if ($punyaSaya)
                        <a href="{{ route('user.poktan.isi', $poktan) }}" class="text-xs font-semibold text-[#2D6A4F] border border-[#2D6A4F]/40 rounded-full px-3.5 py-1.5 hover:bg-[#EAF2EC]">Ubah data saya</a>
                    @elseif ($a->jabatan !== 'ketua')
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('user.poktan.jabatan', [$poktan, $a]) }}">
                                @csrf @method('PATCH')
                                <select name="jabatan" onchange="this.form.submit()" class="text-xs rounded-lg border border-[#E1DCC9] px-2 py-1.5 bg-white">
                                    @foreach (['anggota' => 'Anggota', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara'] as $v => $l)
                                        <option value="{{ $v }}" @selected($a->jabatan === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form method="POST" action="{{ route('user.poktan.anggota.destroy', [$poktan, $a]) }}"
                                  data-judul="Keluarkan anggota?"
                                  data-konfirmasi="{{ $a->nama }} akan dikeluarkan beserta berkas yang sudah diunggahnya. Ia bisa bergabung lagi dengan kode."
                                  data-tombol="Ya, Keluarkan" data-tipe="bahaya">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 flex items-center justify-center" aria-label="Keluarkan anggota"><i class="bi bi-person-dash"></i></button>
                            </form>
                        </div>
                    @endif
                </div>

                @if ($punyaSaya && ($a->file_ktp || $a->file_kk || $a->file_sppt))
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $jenis => $label)
                            @if ($a->{'file_' . $jenis})
                                <a href="{{ route('user.poktan.berkas', [$poktan, $a, $jenis]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] rounded-full px-3 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                                    <i class="bi bi-paperclip"></i> {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Selesai --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
        @if ($siap)
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <p class="font-semibold text-[#1B4332]"><i class="bi bi-check-circle-fill text-[#2D6A4F]"></i> Semua persyaratan terpenuhi</p>
                    <p class="text-sm text-[#5C6B62] mt-1">Setelah diajukan, data anggota tidak dapat diubah dan kode undangan tidak berlaku lagi.</p>
                </div>
                <form method="POST" action="{{ route('user.poktan.selesai', $poktan) }}"
                      data-judul="Ajukan poktan ini?"
                      data-konfirmasi="Setelah diajukan, data anggota tidak dapat diubah sampai admin UPTD selesai memverifikasi."
                      data-tombol="Ya, Ajukan" data-tipe="normal">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-3 rounded-full">
                        Selesai & Ajukan <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        @else
            <p class="font-semibold text-[#14231C]">Belum bisa diajukan</p>
            <ul class="mt-2 space-y-1.5">
                @foreach ($alasan as $pesanAlasan)
                    <li class="text-sm text-amber-800 flex gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $pesanAlasan }}</span></li>
                @endforeach
            </ul>
            <button type="button" disabled class="mt-4 bg-[#1B4332] opacity-40 text-white text-sm font-semibold px-6 py-3 rounded-full cursor-not-allowed">Selesai & Ajukan</button>
        @endif
    </div>

    <script>
        document.querySelectorAll('[data-salin]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var teks = btn.dataset.salin;
                var label = btn.querySelector('span');
                var awal = label.textContent;
                var selesai = function () { label.textContent = 'Tersalin!'; setTimeout(function () { label.textContent = awal; }, 1500); };
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(teks).then(selesai);
                } else {
                    var t = document.createElement('textarea');
                    t.value = teks; document.body.appendChild(t); t.select();
                    document.execCommand('copy'); document.body.removeChild(t); selesai();
                }
            });
        });
    </script>

@endsection