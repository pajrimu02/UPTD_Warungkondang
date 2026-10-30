@php
    $warna = [
        'diajukan' => 'bg-amber-100 text-amber-800',
        'diproses' => 'bg-sky-100 text-sky-800',
        'diterima' => 'bg-emerald-100 text-emerald-800',
        'ditolak' => 'bg-red-100 text-red-800',
    ][$status ?? ''] ?? 'bg-gray-100 text-gray-700';
@endphp
<span class="text-xs px-2.5 py-1 rounded-full whitespace-nowrap {{ $warna }}">{{ ucfirst($status) }}</span>