@extends('layouts.user')

@section('title', 'Anggota Poktan')

@section('content')

    @php
        $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $total = (float) $poktan->anggota->sum('luas_lahan_ha');
        $sisa = (float) ($poktan->desa?->sisaHa() ?? 0);
        $persen = $sisa > 0 ? min(100, round($total / $sisa * 100)) : ($total > 0 ? 100 : 0);
        $lebih = $total > $sisa;
        $adaKetua = $poktan->anggota->contains('jabatan', 'ketua');
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
        $berkas = ['file_ktp' => 'KTP', 'file_kk' => 'Kartu Keluarga (KK)', 'file_sppt' => 'SPPT Sawah'];
    @endphp

    <a href="{{ route('user.poktan') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">{{ $poktan->nama_kelompok }}</h1>
    <p class="text-sm text-[#5C6B62] mb-6"><i class="bi bi-geo-alt"></i> {{ $poktan->desa->nama ?? '-' }}</p>

    @include('user.poktan._stepper', ['aktif' => 2])

    {{-- Ringkasan --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Anggota</p>
            <p class="font-serif text-2xl text-[#1B4332] mt-1">{{ $poktan->anggota->count() }}</p>
        </div>
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Total luas lahan</p>
            <p class="font-serif text-2xl {{ $lebih ? 'text-red-600' : 'text-[#1B4332]' }} mt-1">{{ $fmt($total) }} ha</p>
        </div>
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
            <p class="text-xs text-[#8C9086]">Sisa LBS desa</p>
            <p class="font-serif text-2xl text-[#1B4332] mt-1">{{ $fmt($sisa) }} ha</p>
            <div class="h-2 rounded-full bg-[#EAF2EC] mt-3 overflow-hidden">
                <div class="h-full rounded-full {{ $lebih ? 'bg-red-500' : 'bg-[#2D6A4F]' }}" style="width: {{ $persen }}%"></div>
            </div>
            <p class="text-[11px] mt-1.5 {{ $lebih ? 'text-red-600' : 'text-[#8C9086]' }}">{{ $lebih ? 'Melebihi sisa LBS' : $persen . '% terpakai' }}</p>
        </div>
    </div>

    {{-- Daftar anggota --}}
    <h2 class="font-semibold text-[#14231C] mb-3">Daftar Anggota</h2>
    <div class="space-y-3 mb-8">
        @forelse ($poktan->anggota as $a)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 md:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-[#14231C]">{{ $a->nama }}</p>
                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">{{ $a->jabatan }}</span>
                        </div>
                        <p class="text-xs text-[#5C6B62] mt-1">NIK {{ $a->nik }} · {{ $a->no_hp }} · {{ $fmt($a->luas_lahan_ha) }} ha</p>
                        <p class="text-xs text-[#8C9086] mt-0.5">{{ $a->alamat }}</p>
                    </div>
                    <form method="POST" action="{{ route('user.poktan.anggota.destroy', [$poktan, $a]) }}" onsubmit="return confirm('Hapus anggota ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 flex items-center justify-center" aria-label="Hapus anggota"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $jenis => $label)
                        <a href="{{ route('user.poktan.berkas', [$poktan, $a, $jenis]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] rounded-full px-3 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                            <i class="bi bi-paperclip"></i> {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-[#C9C2A3] rounded-2xl px-6 py-8 text-center text-sm text-[#5C6B62]">
                Belum ada anggota. Mulai dengan menambahkan ketua poktan.
            </div>
        @endforelse
    </div>

    {{-- Form tambah anggota --}}
    <form method="POST" action="{{ route('user.poktan.anggota.store', $poktan) }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-4">
        @csrf
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-[#1B4332]"><i class="bi bi-person-plus"></i> Tambah Anggota</h2>
            @unless ($adaKetua)
                <button type="button" id="isi-saya"
                        data-nama="{{ auth()->user()->name }}" data-nik="{{ auth()->user()->nik }}" data-hp="{{ auth()->user()->no_hp }}"
                        class="text-xs font-semibold text-[#2D6A4F] border border-[#2D6A4F]/40 rounded-full px-3 py-1.5 hover:bg-[#EAF2EC]">
                    Isi dengan data saya (sebagai ketua)
                </button>
            @endunless
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1.5">Nama Lengkap *</label>
                <input id="f-nama" type="text" name="nama" value="{{ old('nama') }}" class="{{ $inputClass }}">
                @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1.5">NIK *</label>
                <input id="f-nik" type="text" name="nik" maxlength="16" value="{{ old('nik') }}" class="{{ $inputClass }}">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1.5">No. HP/WA *</label>
                <input id="f-hp" type="text" name="no_hp" value="{{ old('no_hp') }}" class="{{ $inputClass }}">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1.5">Jabatan *</label>
                <select id="f-jabatan" name="jabatan" class="{{ $inputClass }} bg-white">
                    @foreach (['ketua' => 'Ketua', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara', 'anggota' => 'Anggota'] as $v => $l)
                        <option value="{{ $v }}" @disabled($v === 'ketua' && $adaKetua) @selected(old('jabatan', $adaKetua ? 'anggota' : 'ketua') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                @error('jabatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm mb-1.5">Alamat *</label>
                <input type="text" name="alamat" value="{{ old('alamat') }}" class="{{ $inputClass }}">
                @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1.5">Luas Lahan Sawah (ha) sesuai SPPT *</label>
                <input type="number" step="0.01" min="0.01" name="luas_lahan_ha" value="{{ old('luas_lahan_ha') }}" class="{{ $inputClass }}">
                @error('luas_lahan_ha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-3 pt-1">
            @foreach ($berkas as $field => $label)
                <div>
                    <label class="flex items-center gap-3 border border-dashed border-[#E1DCC9] rounded-xl px-4 py-3.5 cursor-pointer hover:border-[#2D6A4F] transition-colors">
                        <i class="bi bi-cloud-arrow-up text-xl text-[#5C6B62]"></i>
                        <span class="text-sm text-[#5C6B62] truncate">{{ $label }} *</span>
                        <input type="file" name="{{ $field }}" required accept=".jpg,.jpeg,.png,.pdf" data-label="{{ $label }} *" class="sr-only">
                    </label>
                    @error($field) <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <p class="text-xs text-[#8C9086]">JPG, PNG, atau PDF, maksimal 2 MB per berkas.</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-[#2D6A4F] hover:bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full transition">
                <i class="bi bi-plus-lg"></i> Simpan Anggota
            </button>
        </div>
    </form>

    {{-- Selesai --}}
    <div class="mt-8 bg-white border border-[#E1DCC9] rounded-2xl p-6">
        @if ($siap)
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <p class="font-semibold text-[#1B4332]"><i class="bi bi-check-circle-fill text-[#2D6A4F]"></i> Semua persyaratan terpenuhi</p>
                    <p class="text-sm text-[#5C6B62] mt-1">Setelah diajukan, data anggota tidak dapat diubah sampai admin memverifikasi.</p>
                </div>
                <form method="POST" action="{{ route('user.poktan.selesai', $poktan) }}" onsubmit="return confirm('Ajukan poktan ini ke admin UPTD?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-3 rounded-full">
                        Selesai & Ajukan <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        @else
            <p class="font-semibold text-[#14231C]">Belum bisa diajukan</p>
            <ul class="mt-2 space-y-1.5">
                @foreach ($alasan as $a)
                    <li class="text-sm text-amber-800 flex gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $a }}</span></li>
                @endforeach
            </ul>
            <button type="button" disabled class="mt-4 bg-[#1B4332] opacity-40 text-white text-sm font-semibold px-6 py-3 rounded-full cursor-not-allowed">Selesai & Ajukan</button>
        @endif
    </div>

    <script>
        (function () {
            document.querySelectorAll('input[type=file][data-label]').forEach(function (inp) {
                inp.addEventListener('change', function () {
                    var span = inp.previousElementSibling;
                    var f = inp.files[0];
                    if (f && f.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2 MB.');
                        inp.value = '';
                        span.textContent = inp.dataset.label;
                        return;
                    }
                    span.textContent = f ? f.name : inp.dataset.label;
                });
            });

            var tombol = document.getElementById('isi-saya');
            if (tombol) {
                tombol.addEventListener('click', function () {
                    document.getElementById('f-nama').value = tombol.dataset.nama || '';
                    document.getElementById('f-nik').value = tombol.dataset.nik || '';
                    document.getElementById('f-hp').value = tombol.dataset.hp || '';
                    document.getElementById('f-jabatan').value = 'ketua';
                });
            }
        })();
    </script>

@endsection