@extends('layouts.app')

@section('content')
{{-- 
    LOGIC PHP UNTUK TOMBOL BACK 
    Menentukan arah kembali berdasarkan Role dan apakah ada Pasien yang dipilih
--}}
@php
    $role = Auth::user()->role;
    $backUrl = '#';
    $labelBack = 'Kembali ke Daftar Pasien';

    // 1. Tentukan Route Base (Index)
    if ($role === 'staf_administrasi') {
        $backUrl = route('staff_administrasi.datapasien.index');
    } elseif ($role === 'nakes') {
        $backUrl = route('nakes.datapasien');
    } elseif ($role === 'dokter') {
        $backUrl = route('dokter.datapasien');
    } elseif ($role === 'admin') {
        $backUrl = route('admin.patient.index');
    }

    // 2. Jika ada Selected Patient (dari URL), override ke Monitoring Show
    if(isset($selectedPatient) && $selectedPatient) {
        $labelBack = 'Kembali ke Rekam Medis ' . $selectedPatient->name;
        
        if ($role === 'staf_administrasi') {
            $backUrl = route('staff_administrasi.monitoring.show', $selectedPatient->id);
        } elseif ($role === 'nakes') {
            $backUrl = route('nakes.monitoring.show', $selectedPatient->id);
        } elseif ($role === 'dokter') {
            $backUrl = route('dokter.monitoring.show', $selectedPatient->id);
        } elseif ($role === 'admin') {
            // Admin menggunakan User ID berdasarkan controller sebelumnya
            $backUrl = route('admin.patient.show', $selectedPatient->user_id);
        }
    }
@endphp

<div class="max-w-5xl mx-auto p-6">
    
    {{-- TOMBOL BACK --}}
    <div class="mb-6">
        <a href="{{ $backUrl }}" class="inline-flex items-center text-gray-500 hover:text-[#262C5B] transition-colors font-medium group">
            <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center mr-3 group-hover:border-[#262C5B] group-hover:bg-[#262C5B] group-hover:text-white transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </div>
            <span>{{ $labelBack }}</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-[#262C5B] mb-6">Pendaftaran Kunjungan Baru</h2>
        
        <form action="{{ route('visits.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Pasien</label>
                    <select name="patient_id" class="w-full rounded-xl border-gray-300 focus:border-[#5DB8D8] focus:ring focus:ring-sky-200 transition">
                        {{-- Opsi Default --}}
                        <option value="" disabled {{ !isset($selectedPatient) ? 'selected' : '' }}>-- Cari Pasien --</option>
                        
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" 
                                {{ (isset($selectedPatient) && $selectedPatient->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->no_rm }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kunjungan</label>
                    <input type="date" name="visit_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Dokter Tujuan (Sedang Praktik: {{ $currentDayIndo ?? '' }})
                    </label>
                    
                    @if($doctors->isEmpty())
                        <div class="p-3 bg-red-50 text-red-600 text-sm rounded-xl border border-red-200">
                            <i class="fa-solid fa-store-slash mr-1"></i> 
                            Tidak ada dokter yang praktik saat ini ({{ date('H:i') }}).
                        </div>
                    @else
                        <select name="dokter_id" class="w-full rounded-xl border-gray-300" required>
                            <option value="">-- Pilih Dokter Tersedia --</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->user->name }} ({{ $d->specialization }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Kunjungan</label>
                    <select name="visit_type" class="w-full rounded-xl border-gray-300">
                        <option value="baru">Pasien Baru</option>
                        <option value="lama">Pasien Lama</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pembayaran</label>
                    <select name="payment_type" class="w-full rounded-xl border-gray-300">
                        <option value="umum">Umum / Pribadi</option>
                        <option value="bpjs">BPJS Kesehatan</option>
                    </select>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Keluhan Utama (Anamnesa Awal)</label>
                    <textarea name="complaint" rows="3" class="w-full rounded-xl border-gray-300"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#5DB8D8] hover:bg-[#4A9FBD] text-white font-bold py-3 rounded-xl transition-all">
                Buat Kunjungan & Lanjut ke Nakes
            </button>
        </form>
    </div>
</div>
@endsection