@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Solar')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332]">Kelola Pengajuan Solar</h2>

        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua Status</option>
                @foreach (['pending','diproses','diterima','ditolak'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#F5F3EC] text-left">
                <tr>
                    <th class="px-4 py-3">Pemohon</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratSolars as $surat)
                    <tr class="border-t border-[#E1DCC9]">
                        <td class="px-4 py-3">{{ $surat->user->name }}</td>
                        <td class="px-4 py-3">{{ $surat->jumlah_liter_diajukan }} L</td>
                        <td class="px-4 py-3">{{ $surat->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'text-xs px-3 py-1 rounded-full font-medium',
                                'bg-yellow-100 text-yellow-800' => $surat->status === 'pending',
                                'bg-blue-100 text-blue-800' => $surat->status === 'diproses',
                                'bg-green-100 text-green-800' => $surat->status === 'diterima',
                                'bg-red-100 text-red-800' => $surat->status === 'ditolak',
                            ])>{{ ucfirst($surat->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.surat-solar.show', $surat) }}" class="text-[#1B4332] underline">Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-[#5C6B62]">Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $suratSolars->links() }}</div>
@endsection