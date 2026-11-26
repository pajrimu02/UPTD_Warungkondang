@extends('layouts.user')

@section('title', 'Data Diri Anggota')

@section('content')

    @php
        $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
        $berkas = ['file_ktp' => ['ktp', 'KTP'], 'file_kk' => ['kk', 'Kartu Keluarga (KK)'], 'file_sppt' => ['sppt', 'SPPT Sawah']];
        $nilai = fn ($f) => old($f, $saya->{$f});
    @endphp
    <div>
        <x-tombol-kembali :href="route($isKetua ? 'user.poktan.anggota' : 'user.poktan', $isKetua ? $poktan : [])" />
    </div>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Data Diri Anggota</h1>
    <p class="text-sm text-[#5C6B62] mb-6">{{ $poktan->nama_kelompok }} · {{ $poktan->desa->nama ?? '-' }} · Anda: {{ $isKetua ? 'Ketua' : 'Anggota' }}</p>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 text-sm">
            <p class="font-medium mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Data belum tersimpan. Perbaiki yang berikut:</p>
            <ul class="list-disc ml-5 space-y-0.5">
                @foreach ($errors->all() as $pesan) <li>{{ $pesan }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.poktan.isi.simpan', $poktan) }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1.5">Nama Lengkap *</label>
                <input type="text" name="nama" value="{{ $nilai('nama') }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">NIK <span class="text-[#8C9086] font-normal">(sesuai akun)</span></label>
                <input type="text" value="{{ auth()->user()->nik }}" readonly class="{{ $inputClass }} bg-[#F5F3EC]">
            </div>
            <div>
                <label class="block text-sm mb-1.5">Nomor KK *</label>
                <input type="text" name="nomor_kk" maxlength="16" inputmode="numeric" value="{{ $nilai('nomor_kk') }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">Nama Ibu Kandung *</label>
                <input type="text" name="nama_ibu_kandung" value="{{ $nilai('nama_ibu_kandung') }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">Tempat Lahir *</label>
                <input type="text" name="tempat_lahir" value="{{ $nilai('tempat_lahir') }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">Tanggal Lahir *</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $saya->tanggal_lahir?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">No. HP/WA *</label>
                <input type="text" name="no_hp" value="{{ $nilai('no_hp') }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm mb-1.5">Luas Lahan Sawah (ha) sesuai SPPT *</label>
                <input type="number" step="0.01" min="0.01" name="luas_lahan_ha" value="{{ $nilai('luas_lahan_ha') }}" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm mb-1.5">Alamat *</label>
                <input type="text" name="alamat" value="{{ $nilai('alamat') }}" class="{{ $inputClass }}">
            </div>
        </div>

        <div class="space-y-3 pt-1">
            @foreach ($berkas as $field => [$jenis, $label])
                <div>
                    <label class="flex items-center gap-3 border border-dashed border-[#E1DCC9] rounded-xl px-4 py-3.5 cursor-pointer hover:border-[#2D6A4F] transition-colors">
                        <i class="bi {{ $saya->{$field} ? 'bi-check-circle-fill text-[#2D6A4F]' : 'bi-cloud-arrow-up text-[#5C6B62]' }} text-xl"></i>
                        <span class="text-sm text-[#5C6B62] truncate">
                            {{ $label }} {{ $saya->{$field} ? '(sudah diunggah, pilih file baru untuk mengganti)' : '*' }}
                        </span>
                        <input type="file" name="{{ $field }}" accept=".jpg,.jpeg,.png,.pdf" data-label="{{ $label }}" class="sr-only">
                    </label>
                    @if ($saya->{$field})
                        <a href="{{ route('user.poktan.berkas', [$poktan, $saya, $jenis]) }}" target="_blank" class="text-xs text-[#2D6A4F] underline underline-offset-2 mt-1 inline-block">Lihat berkas yang tersimpan</a>
                    @endif
                    <p class="hidden text-xs text-red-600 mt-1" data-galat></p>
                </div>
            @endforeach
            <p class="text-xs text-[#8C9086]">JPG, PNG, atau PDF, maksimal 2 MB per berkas. Berkas Anda disimpan privat dan hanya bisa dibuka oleh Anda dan admin UPTD.</p>
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap pt-1">
            @unless ($isKetua)
                <button type="submit" form="form-keluar" class="text-sm text-red-600">Keluar dari poktan</button>
            @else
                <span></span>
            @endunless
            <button type="submit" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-2.5 rounded-full">
                <i class="bi bi-check2"></i> Simpan Data
            </button>
        </div>
    </form>

    @unless ($isKetua)
        <form id="form-keluar" method="POST" action="{{ route('user.poktan.keluar', $poktan) }}"
              data-judul="Keluar dari poktan?"
              data-konfirmasi="Data dan berkas yang sudah Anda unggah akan dihapus. Anda bisa bergabung lagi dengan kode."
              data-tombol="Ya, Keluar" data-tipe="bahaya">
            @csrf @method('DELETE')
        </form>
    @endunless

    <script>
        document.querySelectorAll('input[type=file][data-label]').forEach(function (inp) {
            inp.addEventListener('change', function () {
                var span = inp.previousElementSibling;
                var galat = inp.closest('div').querySelector('[data-galat]');
                var f = inp.files[0];
                galat.classList.add('hidden');
                if (f && f.size > 2 * 1024 * 1024) {
                    galat.textContent = 'Ukuran file ' + (f.size / 1048576).toFixed(1) + ' MB, melebihi batas 2 MB. Pilih file lain.';
                    galat.classList.remove('hidden');
                    inp.value = '';
                    return;
                }
                if (f) span.textContent = f.name;
            });
        });
    </script>

@endsection