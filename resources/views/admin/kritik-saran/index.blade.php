@extends('layouts.admin')

@section('title', 'Kelola Kritik & Saran')

@section('content')

    @php
        $labelKategori = [
            'surat' => 'Layanan Surat',
            'pupuk_solar' => 'Pupuk & Solar',
            'lainnya' => 'Lainnya',
        ];
    @endphp

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Kelola Kritik & Saran</h1>

        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua Status</option>
                <option value="baru" @selected(request('status') === 'baru')>Baru</option>
                <option value="ditanggapi" @selected(request('status') === 'ditanggapi')>Ditanggapi</option>
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="hidden md:table-header-group bg-[#F5F3EC] text-left text-xs text-[#5C6B62]">
                <tr>
                    <th class="px-5 py-3 font-medium">Pengirim & Isi Pesan</th>
                    <th class="px-3 py-3 font-medium">Kategori</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E1DCC9]">
                @forelse ($kritikSarans as $item)
                    <tr class="block md:table-row px-5 py-4 md:p-0 hover:bg-[#FAF9F4]">
                        <td class="block md:table-cell md:px-5 md:py-3.5 max-w-md">
                            <p class="font-medium text-[#14231C]">{{ $item->user?->name ?? 'Pengguna' }}</p>
                            <p class="text-xs text-[#5C6B62] mt-0.5 line-clamp-2">{{ $item->isi }}</p>
                            <p class="text-[11px] text-[#8C9086] mt-1">{{ $item->created_at->translatedFormat('d M Y, H:i') }} ({{ $item->created_at->diffForHumans() }})</p>
                        </td>

                        <td class="hidden md:table-cell px-3 py-3.5 whitespace-nowrap text-xs font-medium text-[#1B4332]">
                            {{ $labelKategori[$item->kategori] ?? ucfirst($item->kategori) }}
                        </td>

                        <td class="block md:table-cell md:px-3 md:py-3.5 mt-2 md:mt-0 whitespace-nowrap">
                            <span @class([
                                'inline-flex items-center text-xs px-3 py-1 rounded-full font-medium',
                                'bg-amber-100 text-amber-800' => $item->status === 'baru',
                                'bg-emerald-100 text-emerald-800' => $item->status === 'ditanggapi',
                            ])>
                                {{ $item->status === 'ditanggapi' ? 'Ditanggapi' : 'Baru' }}
                            </span>
                            <span class="md:hidden text-xs text-[#5C6B62] ml-1.5">{{ $labelKategori[$item->kategori] ?? ucfirst($item->kategori) }}</span>
                        </td>

                        <td class="block md:table-cell md:px-5 md:py-3.5 mt-3 md:mt-0">
                            <div class="flex items-center gap-1.5 md:justify-end">
                                <a href="{{ route('admin.kritiksaran.show', $item) }}" title="Lihat Detail"
                                   class="w-9 h-9 flex items-center justify-center rounded-full border border-[#E1DCC9] text-[#5C6B62] hover:text-[#1B4332] hover:border-[#2D6A4F] transition">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.kritiksaran.kelola', $item) }}" title="Kelola & Tanggapi"
                                   class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-[#1B4332] text-white text-xs font-medium">
                                    <i class="bi bi-sliders"></i> Kelola
                                </a>

                                <form method="POST" action="{{ route('admin.kritiksaran.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus kritik & saran dari {{ addslashes($item->user?->name ?? 'pengguna ini') }}? Data tidak dapat dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus"
                                            class="w-9 h-9 flex items-center justify-center rounded-full text-[#8C9086] hover:text-red-600 hover:bg-red-50 transition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="4" class="block md:table-cell px-6 py-14 text-center text-sm text-[#5C6B62]">Belum ada kritik atau saran masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kritikSarans->links() }}</div>

@endsection