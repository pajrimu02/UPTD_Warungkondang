@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332]">Profil Saya</h2>
        <a href="{{ route('user.profil.edit') }}" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">
            Edit Profil
        </a>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-xl p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
            <div>
                <dt class="text-[#5C6B62] mb-1">Nama Lengkap</dt>
                <dd class="font-medium text-[#14231C]">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-[#5C6B62] mb-1">Email</dt>
                <dd class="font-medium text-[#14231C]">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-[#5C6B62] mb-1">NIK</dt>
                <dd class="font-medium text-[#14231C]">{{ $user->nik ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-[#5C6B62] mb-1">No. HP</dt>
                <dd class="font-medium text-[#14231C]">{{ $user->no_hp ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-[#5C6B62] mb-1">Bergabung Sejak</dt>
                <dd class="font-medium text-[#14231C]">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
            </div>
        </dl>
    </div>
@endsection