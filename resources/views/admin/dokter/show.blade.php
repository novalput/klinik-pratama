@extends('layouts.app')

@section('title', 'Detail Dokter')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Detail Dokter</h1>
                <p class="text-slate-600 text-sm mt-1">Informasi lengkap dokter.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.dokter.edit', $user->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50">
                    <i class="fas fa-edit text-xs"></i> Edit
                </a>
                <a href="{{ route('admin.dokter.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid gap-8 lg:grid-cols-[380px_1fr]">

            {{-- Foto --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="relative aspect-[3/4] rounded-xl overflow-hidden bg-slate-50">
                    <img
                        src="{{ $user->avatar_url ?? asset('images/ava-default.png') }}"
                        alt="{{ $user->name }}"
                        class="w-full h-full object-cover object-top"
                        onerror="this.src='{{ asset('images/ava-default.png') }}'"
                    >
                    <span class="absolute left-4 top-4 rounded-full bg-sky-600/95 px-3 py-1 text-xs font-semibold text-white shadow-md">
                        {{ $user->dokter?->specialization ?? 'Umum' }}
                    </span>
                </div>
            </div>

            {{-- Detail --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-8">

                {{-- Nama --}}
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Nama</p>
                    <h2 class="text-2xl font-bold text-slate-900 mt-1">{{ $user->name }}</h2>
                </div>

                {{-- Spesialisasi & NIP --}}
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Spesialisasi</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $user->dokter?->specialization ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">NIP</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $user->dokter?->nip ?? '-' }}</p>
                    </div>
                </div>

                {{-- Kontak --}}
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Email</p>
                        <p class="text-sm text-slate-700 mt-1 break-all">{{ $user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">No. HP</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Bio --}}
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Bio</p>
                    <p class="text-sm text-slate-700 mt-2 leading-relaxed max-w-2xl">
                        {{ $user->dokter?->bio ?? '-' }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
