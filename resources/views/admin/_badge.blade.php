@php
    $warna = [
        'draft' => 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]',
        'diajukan' => 'bg-amber-50 text-amber-700 border-amber-200',
        'diproses' => 'bg-sky-50 text-sky-700 border-sky-200',
        'diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'ditolak' => 'bg-red-50 text-red-700 border-red-200',
    ][$status ?? ''] ?? 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]';
@endphp
<span class="text-xs px-2.5 py-1 rounded-full border shrink-0 {{ $warna }}">{{ ucfirst($status) }}</span>