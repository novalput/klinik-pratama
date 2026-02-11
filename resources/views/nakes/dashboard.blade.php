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

    {{-- =================== SECTION DATA EVALUASI =================== --}}
    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-4 tracking-tight">
            Data Evaluasi Pasien
        </h3>

        <div class="flex items-center justify-center h-48 text-gray-400 text-center">
            <div>
                <p class="text-lg font-semibold">Chart belum tersedia</p>
                <p class="text-sm">Fitur evaluasi akan aktif setelah data pemeriksaan pasien tersedia.</p>
            </div>
        </div>
    </section>
</div>

@endsection