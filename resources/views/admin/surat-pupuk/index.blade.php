@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Pupuk')

@section('content')

    <a href="{{ route('admin.surat-pengajuan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Pengajuan Surat Pupuk</h1>

        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua Status</option>
                @foreach (['diajukan', 'diproses', 'diterima', 'ditolak'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="hidden md:table-header-group bg-[#F5F3EC] text-left text-xs text-[#5C6B62]">
                <tr>
                    <th class="px-5 py-3 font-medium">Poktan</th>
                    <th class="px-3 py-3 font-medium">Petani</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E1DCC9]">
                @forelse ($suratPupuks as $surat)
                    <tr class="block md:table-row px-5 py-4 md:p-0 hover:bg-[#FAF9F4]">
                        <td class="block md:table-cell md:px-5 md:py-3.5">
                            <p class="font-medium text-[#14231C]">{{ $surat->poktan->nama_kelompok }}</p>
                            <p class="text-xs text-[#5C6B62]">oleh {{ $surat->user->name }} · {{ $surat->created_at->translatedFormat('d M Y') }}</p>
                        </td>

                        <td class="hidden md:table-cell px-3 py-3.5 whitespace-nowrap">{{ $surat->items_count }} petani</td>

                        <td class="block md:table-cell md:px-3 md:py-3.5 mt-2 md:mt-0">
                            @include('admin._badge', ['status' => $surat->status])
                            <span class="md:hidden text-xs text-[#5C6B62] ml-1">{{ $surat->items_count }} petani</span>
                        </td>

                        <td class="block md:table-cell md:px-5 md:py-3.5 mt-3 md:mt-0">
                            <div class="flex items-center gap-1.5 md:justify-end">
                                <a href="{{ route('admin.surat-pupuk.show', $surat) }}" title="Detail"
                                   class="w-9 h-9 flex items-center justify-center rounded-full border border-[#E1DCC9] text-[#5C6B62] hover:text-[#1B4332] hover:border-[#2D6A4F] transition">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.surat-pupuk.kelola', $surat) }}" title="Kelola"
                                   class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-[#1B4332] text-white text-xs font-medium">
                                    <i class="bi bi-sliders"></i> Kelola
                                </a>
                                <form method="POST" action="{{ route('admin.surat-pupuk.destroy', $surat) }}"
                                      onsubmit="return confirm('Hapus pengajuan pupuk {{ addslashes($surat->poktan->nama_kelompok) }}? Data tidak bisa dikembalikan.')">
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
                        <td colspan="4" class="block md:table-cell px-6 py-14 text-center text-sm text-[#5C6B62]">Belum ada pengajuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $suratPupuks->links() }}</div>

@endsection