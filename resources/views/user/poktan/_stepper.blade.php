@php $langkah = ['Data Poktan', 'Anggota', 'Selesai']; @endphp
<ol class="flex items-center gap-2 mb-8">
    @foreach ($langkah as $i => $nama)
        @php $n = $i + 1; $lewat = $n < $aktif; $kini = $n === $aktif; @endphp
        <li class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <span class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-sm font-semibold
                {{ $lewat ? 'bg-[#2D6A4F] text-white' : ($kini ? 'bg-[#1B4332] text-white ring-4 ring-[#1B4332]/15' : 'bg-white border border-[#E1DCC9] text-[#8C9086]') }}">
                @if ($lewat) <i class="bi bi-check-lg"></i> @else {{ $n }} @endif
            </span>
            <span class="text-sm {{ $kini ? 'font-semibold text-[#1B4332]' : 'text-[#8C9086] hidden sm:inline' }}">{{ $nama }}</span>
            @unless ($loop->last)
                <span class="flex-1 h-px bg-[#E1DCC9]"></span>
            @endunless
        </li>
    @endforeach
</ol>