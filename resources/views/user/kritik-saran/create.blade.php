@extends('layouts.user')

@section('title', 'Kritik & Saran')

@section('content')

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Kritik & Saran</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Sampaikan keluhan, saran, atau pertanyaan kamu untuk pelayanan UPTD.</p>

    {{-- Form kirim --}}
    <form method="POST" action="{{ route('user.kritiksaran.store') }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5 mb-8">
        @csrf

        <div>
            <label class="block text-sm mb-2">Kategori</label>
            <div class="grid grid-cols-3 gap-3">
                @foreach (['surat' => ['bi-envelope-paper', 'Surat'], 'pupuk_solar' => ['bi-fuel-pump', 'Pupuk/Solar'], 'lainnya' => ['bi-three-dots', 'Lainnya']] as $val => [$icon, $label])
                    <label class="kategori-opt relative flex flex-col items-center gap-2 border rounded-xl px-3 py-3 cursor-pointer transition-colors border-[#E1DCC9] hover:border-[#2D6A4F]"
                        data-value="{{ $val }}">
                        <i class="{{ $icon }} bi text-lg text-[#1B4332]"></i>
                        <span class="text-xs">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <input type="hidden" name="kategori" id="kategori-input" value="{{ old('kategori') }}">
            @error('kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Isi Pesan</label>
            <textarea name="isi" rows="4" placeholder="Tulis kritik, saran, atau pertanyaan kamu di sini..." class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">{{ old('isi') }}</textarea>
            @error('isi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">
                <i class="bi bi-send"></i>
                <span>Kirim</span>
            </button>
        </div>
    </form>

    {{-- Riwayat --}}
    <h2 class="text-sm font-medium text-[#5C6B62] mb-3">Riwayat Kiriman Kamu</h2>

    @if ($riwayat->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-chat-square-text text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada kritik/saran yang kamu kirim.</p>
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
            @foreach ($riwayat as $item)
                @php
                    [$catIcon, $catLabel] = match ($item->kategori) {
                        'surat' => ['bi-envelope-paper', 'Surat'],
                        'pupuk_solar' => ['bi-fuel-pump', 'Pupuk/Solar'],
                        'lainnya' => ['bi-three-dots', 'Lainnya'],
                    };
                    $statusColor = $item->status === 'ditanggapi'
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-amber-50 text-amber-700 border-amber-200';
                    $statusLabel = $item->status === 'ditanggapi' ? 'Ditanggapi' : 'Baru';
                @endphp
                <div class="px-5 py-4">
                    <div class="flex items-start gap-4">
                        <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                            <i class="{{ $catIcon }} bi"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-1">
                                <p class="font-medium text-[#14231C]">{{ $catLabel }}</p>
                                <span class="text-xs px-2.5 py-1 rounded-full border {{ $statusColor }} shrink-0">{{ $statusLabel }}</span>
                            </div>
                            <p class="text-sm text-[#14231C]">{{ $item->isi }}</p>
                            <p class="text-xs text-[#5C6B62] mt-2">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</p>

                            @if ($item->tanggapan_admin)
                                <div class="bg-[#F5F3EC] rounded-xl p-3 mt-3">
                                    <p class="flex items-center gap-1.5 text-xs text-[#5C6B62] mb-1"><i class="bi bi-reply"></i>Tanggapan Admin</p>
                                    <p class="text-sm">{{ $item->tanggapan_admin }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <script>
        document.querySelectorAll('.kategori-opt').forEach(function (opt) {
            opt.addEventListener('click', function () {
                var input = document.getElementById('kategori-input');
                var isActive = opt.classList.contains('border-[#2D6A4F]');

                document.querySelectorAll('.kategori-opt').forEach(function (el) {
                    el.classList.remove('border-[#2D6A4F]', 'bg-[#EAF2EC]');
                    el.classList.add('border-[#E1DCC9]');
                });

                if (isActive) {
                    input.value = '';
                } else {
                    opt.classList.remove('border-[#E1DCC9]');
                    opt.classList.add('border-[#2D6A4F]', 'bg-[#EAF2EC]');
                    input.value = opt.dataset.value;
                }
            });
        });
    </script>

@endsection