@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- =================== SECTION SUMMARY USER =================== --}}
    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        <div class="flex items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Ringkasan Pengguna</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Stat 1: Total Pasien --}}
            <div class="flex flex-col p-4 border-r-0 sm:border-r border-gray-100">
                <span class="text-gray-500 text-sm font-medium mb-1">Total Pasien</span>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalPatients }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-pink-100 text-pink-500 font-bold uppercase tracking-wider">All Time</span>
                </div>
            </div>

            {{-- Stat 2: Total Kunjungan --}}
            <div class="flex flex-col p-4 border-r-0 lg:border-r border-gray-100">
                <span class="text-gray-500 text-sm font-medium mb-1">Total Kunjungan</span>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalVisits }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-pink-100 text-pink-500 font-bold uppercase tracking-wider">All Time</span>
                </div>
            </div>

            {{-- Stat 3: Total Kunjungan Mingguan --}}
            <div class="flex flex-col p-4 border-r-0 sm:border-r border-gray-100">
                <span class="text-gray-500 text-sm font-medium mb-1">Total Kunjungan Mingguan</span>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $last7DaysVisits }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-pink-100 text-pink-500 font-bold uppercase tracking-wider">Last 7 Days</span>
                </div>
            </div>

            {{-- Stat 4: Total Nakes --}}
            <div class="flex flex-col p-4">
                <span class="text-gray-500 text-sm font-medium mb-1">Total Tenaga Kesehatan</span>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalNakes }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-pink-100 text-pink-500 font-bold uppercase tracking-wider">All Time</span>
                </div>
            </div>
        </div>
    </section>

    {{-- =================== SECTION TABEL AKTIVITAS =================== --}}
    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 tracking-tight">
                {{ $tableTitle ?? 'Daftar Kunjungan' }}
            </h3>
            {{-- Tombol Lihat Semua (Opsional) --}}
            @php
                $dashboardRoute = '#';
                $role = Auth::user()->role;

                if ($role === 'staf_administrasi') {
                    $dashboardRoute = route('staff_administrasi.datapasien.index');
                } elseif ($role === 'nakes') {
                    $dashboardRoute = route('nakes.datapasien');
                } elseif ($role === 'dokter') {
                    $dashboardRoute = route('dokter.datapasien');
                } elseif ($role === 'admin') {
                    $dashboardRoute = route('admin.patient.index');
                }
            @endphp

            {{-- TOMBOL LIHAT SEMUA YANG SUDAH DIPERBAIKI --}}
            <a href="{{ $dashboardRoute }}" 
            class="text-sm font-bold text-[#5DB8D8] hover:text-[#262C5B] transition-colors flex items-center gap-1">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 font-semibold">Waktu/Tanggal</th>
                        <th class="pb-3 font-semibold">Nama Pasien</th>
                        <th class="pb-3 font-semibold">Keluhan</th>
                        
                        {{-- Kolom Dokter hanya muncul jika bukan di dashboard dokter --}}
                        @if(Auth::user()->role !== 'dokter')
                        <th class="pb-3 font-semibold">Dokter Tujuan</th>
                        @endif
                        
                        <th class="pb-3 font-semibold text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @forelse($visits as $visit)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4">
                            <span class="font-bold block text-gray-800">
                                {{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $visit->created_at->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="py-4">
                            <div class="font-bold">{{ $visit->patient->name }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $visit->patient->no_rm }}</div>
                        </td>
                        <td class="py-4 max-w-[200px] truncate" title="{{ $visit->complaint }}">
                            {{ $visit->complaint ?? '-' }}
                        </td>

                        {{-- Tampilkan Nama Dokter jika bukan login sebagai Dokter --}}
                        @if(Auth::user()->role !== 'dokter')
                        <td class="py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($visit->dokter->user->name ?? 'D', 0, 1) }}
                                </div>
                                <span>{{ $visit->dokter->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        @endif

                        <td class="py-4 text-left flex items-center gap-2">
                            {{-- Logika Badge Status Sederhana --}}
                            @if($visit->medicalRecord && $visit->medicalRecord->diagnosis_icd10)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $visit->status_color }}">
                                    {{ $visit->status_label }}
                                </span>
                            @endif

                            {{-- Tombol Aksi Cepat --}}
                            <a href="{{ route('visits.edit', $visit->id) }}" class="text-gray-400 hover:text-blue-600">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-regular fa-calendar-xmark text-3xl mb-2 opacity-50"></i>
                                <p>Tidak ada data kunjungan untuk ditampilkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection