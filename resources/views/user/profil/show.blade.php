@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Profil Saya</h1>
        <a href="{{ route('user.profil.edit') }}" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full shrink-0">
            <i class="bi bi-pencil"></i>
            <span>Edit Profil</span>
        </a>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        {{-- Header identitas --}}
        <div class="flex items-center gap-4 px-6 py-6 border-b border-[#E1DCC9] bg-[#F5F3EC]">
            <span class="w-14 h-14 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-xl font-semibold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-[#14231C] truncate">{{ $user->name }}</p>
                <p class="text-sm text-[#5C6B62] truncate">{{ $user->email }}</p>
            </div>
        </div>

        {{-- Detail data --}}
        <dl class="divide-y divide-[#E1DCC9]">
            <div class="flex items-center gap-4 px-6 py-4">
                <i class="bi bi-person-vcard text-[#5C6B62] w-5 shrink-0"></i>
                <dt class="text-sm text-[#5C6B62] w-32 shrink-0">NIK</dt>
                <dd class="text-sm font-medium">{{ $user->nik ?: '—' }}</dd>
            </div>
            <div class="flex items-center gap-4 px-6 py-4">
                <i class="bi bi-telephone text-[#5C6B62] w-5 shrink-0"></i>
                <dt class="text-sm text-[#5C6B62] w-32 shrink-0">No. HP</dt>
                <dd class="text-sm font-medium">{{ $user->no_hp ?: '—' }}</dd>
            </div>
            <div class="flex items-center gap-4 px-6 py-4">
                <i class="bi bi-calendar-check text-[#5C6B62] w-5 shrink-0"></i>
                <dt class="text-sm text-[#5C6B62] w-32 shrink-0">Bergabung</dt>
                <dd class="text-sm font-medium">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
            </div>
        </dl>
    </div>

@endsection