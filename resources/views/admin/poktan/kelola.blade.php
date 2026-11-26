@extends('layouts.admin')

@section('title', 'Kelola Kelompok Tani')

@section('content')

    @php
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $persen = $minLuas > 0 ? min(100, round($totalLuas / $minLuas * 100)) : 0;
        $penuh = $jumlah >= $maksAnggota;
        $input = 'w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm';
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <x-tombol-kembali :href="route('admin.poktan.index')" />

    <div class="mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-2xl font-semibold text-[#1B4332]">Kelola: {{ $poktan->nama_kelompok }}</h1>
            @include('admin._badge', ['status' => $poktan->status])
        </div>
        <p class="text-sm text-[#5C6B62] mt-1">
            Desa {{ $poktan->desa?->nama ?? '—' }} · Ketua: {{ $poktan->nama_ketua ?: '—' }}
            @if ($poktan->nomor_sk) · Nomor SK: <b class="text-[#1B4332]">{{ $poktan->nomor_sk }}</b> @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: Verifikasi Anggota & Tambah Jalur Petugas --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 3 Metrik Kapasitas --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Anggota</p>
                    <p class="font-serif text-2xl text-[#1B4332] mt-1">{{ $jumlah }} <span class="text-base text-[#8C9086]">/ {{ $maksAnggota }}</span></p>
                </div>
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Total luas lahan</p>
                    <p class="font-serif text-2xl {{ $totalLuas >= $minLuas ? 'text-[#2D6A4F]' : 'text-[#1B4332]' }} mt-1">{{ $fmt($totalLuas) }} ha</p>
                    <div class="h-2 rounded-full bg-[#EAF2EC] mt-3 overflow-hidden">
                        <div class="h-full rounded-full {{ $totalLuas >= $minLuas ? 'bg-[#2D6A4F]' : 'bg-[#C9A227]' }}" style="width: {{ $persen }}%"></div>
                    </div>
                    <p class="text-[11px] mt-1.5 text-[#8C9086]">Minimal {{ $fmt($minLuas) }} ha</p>
                </div>
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Sisa batas desa</p>
                    @if ($sisaDesa !== null)
                        <p class="font-serif text-2xl {{ $totalLuas > $sisaDesa ? 'text-red-600' : 'text-[#1B4332]' }} mt-1">{{ $fmt($sisaDesa) }} ha</p>
                        <p class="text-[11px] mt-3 text-[#8C9086]">{{ $poktan->desa->memakaiBatasBawaan() ? 'Batas sementara' : 'Luas baku sawah' }}</p>
                    @else
                        <p class="font-serif text-2xl text-[#8C9086] mt-1">—</p>
                        <p class="text-[11px] mt-3 text-[#8C9086]">Belum ada batas desa</p>
                    @endif
                </div>
            </div>

            {{-- Form Tambah Anggota Massal (Jalur Dibantu Petugas) --}}
            @if ($bolehUbah)
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-1">
                        <h2 class="font-semibold text-lg text-[#1B4332]">
                            <i class="bi bi-person-plus"></i> Tambah Anggota (Dibantu Petugas)
                        </h2>
                        @unless ($penuh)
                            <button type="button" id="btnTambahBaris"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold bg-[#EAF2EC] text-[#1B4332] hover:bg-[#1B4332] hover:text-white px-3.5 py-1.5 rounded-full transition">
                                <i class="bi bi-plus-lg"></i> Tambah Form Petani Lain
                            </button>
                        @endunless
                    </div>
                    <p class="text-xs text-[#5C6B62] mb-4">
                        Untuk petani yang tidak punya HP. Anda dapat menambahkan beberapa petani sekaligus lalu menyimpannya bersamaan.
                    </p>

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <ul class="list-disc ml-5 space-y-0.5">@foreach ($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach</ul>
                        </div>
                    @endif

                    @if ($penuh)
                        <p class="text-sm text-[#5C6B62]">Jumlah anggota sudah mencapai batas maksimal ({{ $maksAnggota }} orang).</p>
                    @else
                        <form method="POST" action="{{ route('admin.poktan.anggota.store', $poktan) }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <div id="wadahAnggota" class="space-y-6">
                                {{-- Blok Petani #1 --}}
                                <div class="blok-petani border border-[#E1DCC9] bg-[#FAF9F4] rounded-2xl p-4 space-y-4">
                                    <div class="flex items-center justify-between border-b border-[#E1DCC9] pb-2">
                                        <span class="judul-petani text-xs font-bold uppercase tracking-wider text-[#1B4332]">Petani #1</span>
                                        <button type="button" class="btn-hapus-baris hidden text-xs text-red-600 hover:underline">
                                            <i class="bi bi-trash"></i> Hapus baris ini
                                        </button>
                                    </div>

                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Nama Lengkap *</label>
                                            <input type="text" name="anggota[0][nama]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">NIK (16 digit) *</label>
                                            <input type="text" name="anggota[0][nik]" maxlength="16" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Nomor KK (16 digit) *</label>
                                            <input type="text" name="anggota[0][nomor_kk]" maxlength="16" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Nama Ibu Kandung *</label>
                                            <input type="text" name="anggota[0][nama_ibu_kandung]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Tempat Lahir *</label>
                                            <input type="text" name="anggota[0][tempat_lahir]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Tanggal Lahir *</label>
                                            <input type="date" name="anggota[0][tanggal_lahir]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">No. HP (bisa pakai No. Ketua) *</label>
                                            <input type="text" name="anggota[0][no_hp]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Luas Lahan (ha) sesuai SPPT *</label>
                                            <input type="number" name="anggota[0][luas_lahan_ha]" step="0.01" min="0.01" required class="{{ $input }} bg-white">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium mb-1">Alamat *</label>
                                            <input type="text" name="anggota[0][alamat]" required class="{{ $input }} bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1">Jabatan *</label>
                                            <select name="anggota[0][jabatan]" class="{{ $input }} bg-white">
                                                <option value="anggota" selected>Anggota</option>
                                                <option value="sekretaris">Sekretaris</option>
                                                <option value="bendahara">Bendahara</option>
                                                <option value="ketua">Ketua</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-[#E1DCC9]">
                                        <div>
                                            <label class="block text-xs mb-1 text-[#5C6B62]">KTP *</label>
                                            <input type="file" name="anggota[0][file_ktp]" required accept=".jpg,.jpeg,.png,.pdf" class="text-xs w-full">
                                        </div>
                                        <div>
                                            <label class="block text-xs mb-1 text-[#5C6B62]">KK *</label>
                                            <input type="file" name="anggota[0][file_kk]" required accept=".jpg,.jpeg,.png,.pdf" class="text-xs w-full">
                                        </div>
                                        <div>
                                            <label class="block text-xs mb-1 text-[#5C6B62]">SPPT Sawah *</label>
                                            <input type="file" name="anggota[0][file_sppt]" required accept=".jpg,.jpeg,.png,.pdf" class="text-xs w-full">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-2">
                                <p class="text-xs text-[#8C9086]">Format JPG, PNG, atau PDF, maksimal 2 MB per berkas.</p>
                                <button type="submit" class="bg-[#1B4332] text-white text-sm font-medium px-6 py-2.5 rounded-full shrink-0">
                                    Simpan Semua Anggota
                                </button>
                            </div>
                        </form>

                        <script>
                            (function () {
                                const wadah = document.getElementById('wadahAnggota');
                                const btnTambah = document.getElementById('btnTambahBaris');
                                const sisaKuota = {{ max(0, $maksAnggota - $jumlah) }};

                                function perbaruiIndex() {
                                    const blokList = wadah.querySelectorAll('.blok-petani');
                                    blokList.forEach((blok, idx) => {
                                        blok.querySelector('.judul-petani').textContent = 'Petani #' + (idx + 1);
                                        const btnHapus = blok.querySelector('.btn-hapus-baris');
                                        if (blokList.length > 1) {
                                            btnHapus.classList.remove('hidden');
                                        } else {
                                            btnHapus.classList.add('hidden');
                                        }
                                        blok.querySelectorAll('input, select').forEach(el => {
                                            if (el.name) {
                                                el.name = el.name.replace(/anggota\[\d+\]/, 'anggota[' + idx + ']');
                                            }
                                        });
                                    });
                                }

                                btnTambah?.addEventListener('click', () => {
                                    const jumlahBlok = wadah.querySelectorAll('.blok-petani').length;
                                    if (jumlahBlok >= sisaKuota) {
                                        alert('Maksimal hanya bisa menambah ' + sisaKuota + ' anggota lagi.');
                                        return;
                                    }
                                    const klon = wadah.firstElementChild.cloneNode(true);
                                    klon.querySelectorAll('input').forEach(inp => inp.value = '');
                                    klon.querySelector('select').value = 'anggota';
                                    wadah.appendChild(klon);
                                    perbaruiIndex();
                                });

                                wadah?.addEventListener('click', (e) => {
                                    if (e.target.closest('.btn-hapus-baris')) {
                                        e.target.closest('.blok-petani').remove();
                                        perbaruiIndex();
                                    }
                                });
                            })();
                        </script>
                    @endif
                </div>
            @endif

            {{-- Manajemen Daftar Anggota (Verifikasi, Edit & Hapus) --}}
            <div id="anggota" class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h2 class="font-semibold text-lg text-[#1B4332] mb-4">Verifikasi & Edit Data Anggota</h2>

                <div class="divide-y divide-[#E1DCC9]">
                    @forelse ($poktan->anggota as $a)
                        <div class="py-4 first:pt-0 last:pb-0 {{ $a->status === 'nonaktif' ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-medium text-[#14231C]">{{ $a->nama ?: '(belum diisi)' }}</p>
                                        <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">{{ $a->jabatan }}</span>
                                        @if ($a->status === 'menunggu')
                                            <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-amber-50 text-amber-700">menunggu diisi</span>
                                        @endif
                                        @if ($a->dibantu_petugas)
                                            <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-violet-50 text-violet-700">dibantu petugas</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#5C6B62] mt-1">
                                        NIK {{ $a->nik ?: '—' }} · KK {{ $a->nomor_kk ?: '—' }} · HP {{ $a->no_hp ?: '—' }} · <b class="text-[#14231C]">{{ $fmt($a->luas_lahan_ha ?? 0) }} ha</b>
                                    </p>
                                    @if ($a->tempat_lahir)
                                        <p class="text-xs text-[#8C9086] mt-0.5">{{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d-m-Y') }} · Ibu: {{ $a->nama_ibu_kandung }} · {{ $a->alamat ?: '—' }}</p>
                                    @endif
                                </div>

                                @if ($bolehUbah)
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" onclick="toggleEditAnggota({{ $a->id }})" title="Edit data anggota"
                                                class="w-9 h-9 flex items-center justify-center rounded-full text-[#5C6B62] hover:text-[#1B4332] hover:bg-[#EAF2EC] transition">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        @if ($a->jabatan !== 'ketua')
                                            <form method="POST" action="{{ route('admin.poktan.anggota.destroy', [$poktan, $a]) }}" onsubmit="return confirm('Hapus anggota {{ addslashes($a->nama) }} beserta berkasnya?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus anggota" class="w-9 h-9 flex items-center justify-center rounded-full text-[#8C9086] hover:text-red-600 hover:bg-red-50 transition">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 mt-2.5">
                                @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $j => $l)
                                    @if ($a->{'file_' . $j})
                                        <a href="{{ route('admin.poktan.berkas', [$poktan, $a, $j]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] rounded-full px-3 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                                            <i class="bi bi-paperclip"></i> {{ $l }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs border border-red-200 bg-red-50 rounded-full px-3 py-1 text-red-700"><i class="bi bi-x-circle"></i> {{ $l }} belum ada</span>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Panel Inline Edit Data & Berkas Anggota --}}
                            @if ($bolehUbah)
                                <div id="form-edit-{{ $a->id }}" class="hidden mt-4 p-4 rounded-2xl border border-[#E1DCC9] bg-[#FAF9F4]">
                                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-[#E1DCC9]">
                                        <p class="text-xs font-bold uppercase tracking-wider text-[#1B4332]">Edit Data: {{ $a->nama }}</p>
                                        <button type="button" onclick="toggleEditAnggota({{ $a->id }})" class="text-xs text-[#5C6B62] hover:text-red-600">Tutup</button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.poktan.anggota.update', [$poktan, $a]) }}" enctype="multipart/form-data" class="space-y-3">
                                        @csrf @method('PUT')

                                        <div class="grid sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Nama Lengkap *</label>
                                                <input type="text" name="nama" value="{{ $a->nama }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">NIK (16 digit) *</label>
                                                <input type="text" name="nik" value="{{ $a->nik }}" maxlength="16" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Nomor KK (16 digit) *</label>
                                                <input type="text" name="nomor_kk" value="{{ $a->nomor_kk }}" maxlength="16" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Nama Ibu Kandung *</label>
                                                <input type="text" name="nama_ibu_kandung" value="{{ $a->nama_ibu_kandung }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Tempat Lahir *</label>
                                                <input type="text" name="tempat_lahir" value="{{ $a->tempat_lahir }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Tanggal Lahir *</label>
                                                <input type="date" name="tanggal_lahir" value="{{ $a->tanggal_lahir?->format('Y-m-d') }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">No. HP *</label>
                                                <input type="text" name="no_hp" value="{{ $a->no_hp }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Luas Lahan (ha) *</label>
                                                <input type="number" name="luas_lahan_ha" value="{{ $a->luas_lahan_ha }}" step="0.01" min="0.01" required class="{{ $input }} bg-white">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs font-medium mb-1">Alamat *</label>
                                                <input type="text" name="alamat" value="{{ $a->alamat }}" required class="{{ $input }} bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Jabatan *</label>
                                                <select name="jabatan" class="{{ $input }} bg-white">
                                                    @foreach (['anggota' => 'Anggota', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara', 'ketua' => 'Ketua'] as $v => $l)
                                                        <option value="{{ $v }}" @selected($a->jabatan === $v)>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-[#E1DCC9]">
                                            @foreach (['file_ktp' => 'Ganti KTP (opsional)', 'file_kk' => 'Ganti KK (opsional)', 'file_sppt' => 'Ganti SPPT (opsional)'] as $f => $l)
                                                <div>
                                                    <label class="block text-[11px] mb-1 text-[#5C6B62]">{{ $l }}</label>
                                                    <input type="file" name="{{ $f }}" accept=".jpg,.jpeg,.png,.pdf" class="text-xs w-full">
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <button type="button" onclick="toggleEditAnggota({{ $a->id }})" class="px-4 py-1.5 text-xs rounded-full border border-[#E1DCC9] bg-white text-[#5C6B62]">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-5 py-1.5 text-xs font-medium rounded-full bg-[#1B4332] text-white">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-sm text-center text-[#5C6B62]">Belum ada anggota.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Syarat & Panel Keputusan / TTD SK --}}
        <div class="space-y-6 h-fit">
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-4">Syarat Persetujuan</h3>
                <ul class="space-y-3">
                    @foreach ($syarat as $s)
                        <li class="flex gap-2.5 text-sm">
                            <i class="bi {{ $s['ok'] ? 'bi-check-circle-fill text-emerald-600' : 'bi-x-circle-fill text-red-500' }} mt-0.5"></i>
                            <div>
                                <p class="{{ $s['ok'] ? 'text-[#14231C]' : 'text-red-700 font-medium' }}">{{ $s['label'] }}</p>
                                <p class="text-xs text-[#8C9086]">{{ $s['ket'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-4">Keputusan & Penerbitan SK</h3>

                @if ($poktan->status === 'diajukan')
                    <form method="POST" action="{{ route('admin.poktan.update', $poktan) }}" class="space-y-4">
                        @csrf @method('PUT')

                        <div>
                            <label class="block text-sm mb-1">Catatan <span class="text-[#5C6B62]">(wajib jika ditolak)</span></label>
                            <textarea name="catatan_admin" rows="4" class="{{ $input }}">{{ old('catatan_admin') }}</textarea>
                            @error('catatan_admin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" name="keputusan" value="ditolak" class="flex-1 bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-2 rounded-full">Tolak</button>
                            <button type="submit" name="keputusan" value="disetujui" class="flex-1 bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Setujui</button>
                        </div>
                    </form>
                @elseif ($poktan->status === 'draft')
                    <p class="text-sm text-[#5C6B62]">Poktan masih berstatus <b>draft</b>. Keputusan baru bisa diambil setelah ketua mengajukan.</p>
                @elseif ($poktan->status === 'disetujui')
                    <div class="space-y-3">
                        <a href="{{ route('admin.poktan.sk-preview', $poktan) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full hover:border-[#2D6A4F]">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Draft PDF SK ({{ $poktan->nomor_sk }})
                        </a>

                        @if (! $poktan->ttd_dikirim_at)
                            <form method="POST" action="{{ route('admin.poktan.tahap', $poktan) }}">
                                @csrf
                                <input type="hidden" name="aksi" value="kirim_kepala">
                                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
                                    Kirim SK ke Kepala UPTD (TTD)
                                </button>
                            </form>
                        @elseif (! $poktan->ttd_kembali_at)
                            <div class="text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-3.5 py-3 leading-relaxed">
                                <p class="font-semibold flex items-center gap-1.5"><i class="bi bi-hourglass-split"></i> Terkirim ke Kepala UPTD</p>
                                <p class="mt-1">SK sudah diteruskan untuk tanda tangan Kepala UPTD pada {{ $poktan->ttd_dikirim_at->translatedFormat('d M Y, H:i') }}.</p>
                            </div>
                        @elseif (! $poktan->diserahkan_at)
                            <a href="{{ asset('storage/' . $poktan->file_sk_resmi) }}" target="_blank"
                               class="w-full inline-flex items-center justify-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium px-4 py-2 rounded-full">
                                <i class="bi bi-eye"></i> Periksa Scan SK Kepala UPTD
                            </a>
                            <form method="POST" action="{{ route('admin.poktan.tahap', $poktan) }}">
                                @csrf
                                <input type="hidden" name="aksi" value="kirim_petani">
                                <button type="submit" class="w-full bg-[#2D6A4F] text-white text-sm px-4 py-2.5 rounded-full">
                                    Kirim SK Resmi ke Petani
                                </button>
                            </form>
                        @else
                            <p class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-3.5 py-2.5">
                                <i class="bi bi-check-circle-fill"></i> SK resmi sudah dikirim ke Poktan pada {{ $poktan->diserahkan_at->translatedFormat('d M Y, H:i') }}.
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-[#5C6B62]">Status: <b>Ditolak</b>.</p>
                    @if ($poktan->catatan_admin)
                        <p class="text-sm text-[#5C6B62] mt-2"><span class="font-medium text-[#1B4332]">Alasan:</span> {{ $poktan->catatan_admin }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleEditAnggota(id) {
            const el = document.getElementById('form-edit-' + id);
            if (el) el.classList.toggle('hidden');
        }
    </script>

@endsection