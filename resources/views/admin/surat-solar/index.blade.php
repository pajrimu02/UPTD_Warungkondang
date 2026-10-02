@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Solar')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Pengajuan Surat Solar</h1>

        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="">Semua Status</option>
                @foreach (['diajukan','diproses','diterima','ditolak'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($suratSolars as $surat)
            @php
                $statusColor = match ($surat->status) {
                    'diajukan' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'diproses' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                    default => 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]',
                };
                $spkColor = match ($surat->spk_label) {
                    'layak' => 'bg-emerald-50 text-emerald-700',
                    'perlu_ditinjau' => 'bg-amber-50 text-amber-700',
                    'tidak_layak' => 'bg-red-50 text-red-700',
                    default => 'bg-[#F5F3EC] text-[#5C6B62]',
                };
                $spkLabelText = match ($surat->spk_label) {
                    'layak' => 'Layak',
                    'perlu_ditinjau' => 'Perlu Ditinjau',
                    'tidak_layak' => 'Tidak Layak',
                    default => '—',
                };
            @endphp
            <a href="{{ route('admin.surat-solar.show', $surat) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
                <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                    <i class="bi bi-fuel-pump"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-[#14231C]">{{ $surat->nama_pemohon ?? $surat->user->name }}</p>
                    <p class="text-sm text-[#5C6B62]">{{ $surat->usulan_volume_konsumsi }} L/{{ $surat->volume_periode }} · {{ $surat->created_at->translatedFormat('d M Y') }}</p>
                </div>
                @if ($surat->spk_skor !== null)
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $spkColor }} shrink-0">SPK: {{ $spkLabelText }} ({{ $surat->spk_skor }})</span>
                @endif
                <span class="text-xs px-2.5 py-1 rounded-full border {{ $statusColor }} shrink-0">{{ ucfirst($surat->status) }}</span>
                <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
            </a>
        @empty
            <div class="px-6 py-14 text-center">
                <p class="text-sm text-[#5C6B62]">Belum ada pengajuan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $suratSolars->links() }}</div>

@endsection