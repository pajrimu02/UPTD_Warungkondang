@extends('layouts.admin')

@section('title', 'Akun Petani')

@section('content')

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Akun Petani</h1>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau NIK"
                   class="text-sm rounded-lg border border-[#E1DCC9] px-3 py-2 w-64">
            <button class="text-sm bg-[#1B4332] text-white px-4 py-2 rounded-full">Cari</button>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="hidden md:table-header-group bg-[#F5F3EC] text-left text-xs text-[#5C6B62]">
                <tr>
                    <th class="px-5 py-3 font-medium">Petani</th>
                    <th class="px-3 py-3 font-medium">NIK</th>
                    <th class="px-3 py-3 font-medium">Poktan</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E1DCC9]">
                @forelse ($petanis as $p)
                    <tr class="block md:table-row px-5 py-4 md:p-0 hover:bg-[#FAF9F4] {{ $p->aktif ? '' : 'opacity-60' }}">
                        <td class="block md:table-cell md:px-5 md:py-3.5">
                            <p class="font-medium text-[#14231C]">{{ $p->name }}</p>
                            <p class="text-xs text-[#5C6B62]">{{ $p->email }}</p>
                        </td>
                        <td class="hidden md:table-cell px-3 py-3.5 whitespace-nowrap">{{ $p->nik ?: '—' }}</td>
                        <td class="hidden md:table-cell px-3 py-3.5">{{ $p->poktan?->nama_kelompok ?? '—' }}</td>
                        <td class="block md:table-cell md:px-3 md:py-3.5 mt-2 md:mt-0">
                            @if ($p->aktif)
                                <span class="text-xs px-2.5 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">Aktif</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full border bg-slate-100 text-slate-600 border-slate-200">Nonaktif</span>
                            @endif
                        </td>
                        <td class="block md:table-cell md:px-5 md:py-3.5 mt-3 md:mt-0">
                            <div class="flex items-center gap-1.5 md:justify-end flex-wrap">
                                <a href="{{ route('admin.petani.edit', $p) }}" class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-[#1B4332] text-white text-xs font-medium">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.petani.reset-password', $p) }}" onsubmit="return confirm('Reset password {{ addslashes($p->name) }}? Password baru ditampilkan satu kali.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full border border-[#E1DCC9] text-[#1B4332] text-xs font-medium hover:border-[#2D6A4F]">
                                        <i class="bi bi-key"></i> Reset
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.petani.status', $p) }}"
                                      onsubmit="return confirm('{{ $p->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ addslashes($p->name) }}?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full border text-xs font-medium {{ $p->aktif ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                        <i class="bi {{ $p->aktif ? 'bi-slash-circle' : 'bi-check-circle' }}"></i> {{ $p->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="5" class="block md:table-cell px-6 py-14 text-center text-sm text-[#5C6B62]">Belum ada akun petani.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $petanis->links() }}</div>

@endsection