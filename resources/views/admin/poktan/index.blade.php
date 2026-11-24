@extends('layouts.admin')

@section('title', 'Kelompok Tani')

@section('content')

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Pengajuan Kelompok Tani</h1>

        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua (kecuali draft)</option>
                @foreach (['diajukan', 'disetujui', 'ditolak', 'draft'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="hidden md:table-header-group bg-[#F5F3EC] text-left text-xs text-[#5C6B62]">
                <tr>
                    <th class="px-5 py-3 font-medium">Kelompok Tani</th>
                    <th class="px-3 py-3 font-medium">Anggota & Luas</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E1DCC9]">
                @forelse ($poktans as $p)
                    @php
                        $bolehHapus = in_array($p->status, ['draft', 'ditolak'], true);
                    @endphp
                    <tr class="block md:table-row px-5 py-4 md:p-0 hover:bg-[#FAF9F4]">
                        <td class="block md:table-cell md:px-5 md:py-3.5">
                            <p class="font-medium text-[#14231C]">{{ $p->nama_kelompok }}</p>
                            <p class="text-xs text-[#5C6B62]">
                                Desa {{ $p->desa?->nama ?? '—' }}
                                @if ($p->pengaju) · oleh {{ $p->pengaju->name }} @endif
                                · {{ $p->created_at->translatedFormat('d M Y') }}
                            </p>
                            @if ($p->nomor_sk)
                                <p class="text-[11px] font-medium text-[#2D6A4F] mt-0.5">SK: {{ $p->nomor_sk }}</p>
                            @endif
                        </td>

                        <td class="hidden md:table-cell px-3 py-3.5 whitespace-nowrap">
                            <p class="text-[#14231C]">{{ $p->anggota_count }} anggota</p>
                            <p class="text-xs text-[#8C9086]">{{ number_format((float) $p->luas_lahan_ha, 2, ',', '.') }} ha</p>
                        </td>

                        <td class="block md:table-cell md:px-3 md:py-3.5 mt-2 md:mt-0">
                            @include('admin._badge', ['status' => $p->status])
                            <span class="md:hidden text-xs text-[#5C6B62] ml-1">
                                {{ $p->anggota_count }} anggota · {{ number_format((float) $p->luas_lahan_ha, 2, ',', '.') }} ha
                            </span>
                        </td>

                        <td class="block md:table-cell md:px-5 md:py-3.5 mt-3 md:mt-0">
                            <div class="flex items-center gap-1.5 md:justify-end">
                                <a href="{{ route('admin.poktan.show', $p) }}" title="Detail"
                                class="w-9 h-9 flex items-center justify-center rounded-full border border-[#E1DCC9] text-[#5C6B62] hover:text-[#1B4332] hover:border-[#2D6A4F] transition">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.poktan.kelola', $p) }}" title="Kelola"
                                class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-[#1B4332] text-white text-xs font-medium">
                                    <i class="bi bi-sliders"></i> Kelola
                                </a>

                                @if ($bolehHapus)
                                    <form method="POST" action="{{ route('admin.poktan.destroy', $p) }}"
                                          onsubmit="return confirm('Hapus pengajuan poktan {{ addslashes($p->nama_kelompok) }} beserta berkas anggotanya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="w-9 h-9 flex items-center justify-center rounded-full text-[#8C9086] hover:text-red-600 hover:bg-red-50 transition">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span title="Poktan aktif/diajukan tidak dapat dihapus"
                                          class="w-9 h-9 flex items-center justify-center rounded-full text-slate-300 cursor-not-allowed">
                                        <i class="bi bi-trash"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="4" class="block md:table-cell px-6 py-14 text-center text-sm text-[#5C6B62]">Belum ada poktan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $poktans->links() }}</div>

@endsection