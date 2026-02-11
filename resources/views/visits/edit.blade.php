@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-8">
    
    {{-- LOGIC: Tentukan Route Kembali berdasarkan Role --}}
    @php
        $backRoute = url()->previous();
        $role = Auth::user()->role;
        $patientId = $visit->patient_id;

        if ($role === 'dokter') {
            $backRoute = route('dokter.datapasien'); // Ubah ke datapasien agar lebih umum
        } elseif ($role === 'staf_administrasi') {
            $backRoute = route('staff_administrasi.datapasien');
        } elseif ($role === 'nakes') {
            $backRoute = route('nakes.datapasien');
        } elseif ($role === 'admin') {
            $backRoute = route('admin.patient.index'); 
        }
        
        // Helper untuk cek dokter assignment
        $loggedInDokterId = ($role === 'dokter' && Auth::user()->dokter) ? Auth::user()->dokter->id : null;
        $isAssignedDoctor = $visit->dokter_id == $loggedInDokterId;
    @endphp
    
    @php
        // Jika user adalah admin, kita paksa semua flag permission menjadi false
        if (Auth::user()->role === 'admin') {
            $isAdmin = false;
            $isNurse = false;
            $isDokter = false;
        }
    @endphp
    {{-- Info Header dengan Tombol Kembali --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ $backRoute }}" 
               class="flex items-center justify-center w-12 h-12 bg-white text-gray-400 rounded-2xl shadow-sm border border-gray-100 hover:bg-[#5DB8D8] hover:text-white hover:border-[#5DB8D8] transition-all duration-200 group"
               title="Kembali">
                <i class="fa-solid fa-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i>
            </a>

            <div>
                <h1 class="text-2xl font-bold text-[#262C5B]">Rekam Medis Pasien</h1>
                <p class="text-gray-500 font-medium">
                    {{ $visit->patient->name }} <span class="mx-2 text-gray-300">|</span> 
                    {{ $visit->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }} <span class="mx-2 text-gray-300">|</span>
                    {{ \Carbon\Carbon::parse($visit->patient->dob)->age }} Tahun
                </p>
            </div>
        </div>
        
        <div class="text-right hidden md:block">
            <span class="block text-xs text-gray-400 uppercase tracking-wider">Anda Login Sebagai</span>
            <span class="font-bold text-[#5DB8D8]">
                @if(Auth::user()->isNakes())
                    {{ Auth::user()->nakes->position }}
                @else
                    {{ ucfirst(Auth::user()->role) }}
                @endif
            </span>
        </div>
    </div>

    <form action="{{ route('visits.update', $visit->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- ========================================================= --}}
        {{-- BAGIAN 1: ADMINISTRASI (Hak Akses: STAF ADMIN) --}}
        {{-- ========================================================= --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-gray-400"></div>
            <h3 class="text-lg font-bold text-gray-800 mb-6 pl-4">Data Kunjungan (Administrasi)</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pl-4">
                {{-- Gunakan method yang benar: canEditAdministrasiData --}}
                @php $isAdmin = $user->canEditAdministrasiData(); @endphp

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" name="visit_date" value="{{ $visit->visit_date->format('Y-m-d') }}" 
                        class="w-full rounded-xl border-gray-300 {{ !$isAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : '' }}"
                        {{ !$isAdmin ? 'readonly' : '' }}>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jenis Pasien</label>
                     <select name="visit_type" class="w-full rounded-xl border-gray-300 {{ !$isAdmin ? 'bg-gray-50 text-gray-500 pointer-events-none' : '' }}">
                        <option value="baru" {{ $visit->visit_type == 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="lama" {{ $visit->visit_type == 'lama' ? 'selected' : '' }}>Lama</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pembayaran</label>
                    <select name="payment_type" class="w-full rounded-xl border-gray-300 {{ !$isAdmin ? 'bg-gray-50 text-gray-500 pointer-events-none' : '' }}">
                        <option value="umum" {{ $visit->payment_type == 'umum' ? 'selected' : '' }}>Umum / Pribadi</option>
                        <option value="bpjs" {{ $visit->payment_type == 'bpjs' ? 'selected' : '' }}>BPJS Kesehatan</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Keluhan Utama</label>
                    <textarea name="complaint" rows="2" 
                        class="w-full rounded-xl border-gray-300 {{ !$isAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : '' }}"
                        {{ !$isAdmin ? 'readonly' : '' }}>{{ $visit->complaint }}</textarea>
                </div>
            </div>
            
            @if(!$isAdmin)
                <div class="absolute top-4 right-4 flex items-center gap-2 text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                    <i class="fa-solid fa-lock"></i> Read Only
                </div>
            @endif
        </div>


        {{-- ========================================================= --}}
        {{-- BAGIAN 2: PEMERIKSAAN AWAL (Hak Akses: PERAWAT & Assigned Dokter) --}}
        {{-- ========================================================= --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-yellow-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-2 h-full bg-yellow-400"></div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-6 pl-4 flex items-center gap-2">
                Pemeriksaan Awal (Nakes)
            </h3>

            @php 
                $profile = $visit->patient->patientProfile;
                $mr = $visit->medicalRecord;
                
                // Logic Permission:
                // 1. Perawat asli -> Boleh
                // 2. Dokter -> HANYA jika dia assigned dokter (Dokter lain read-only)
                $basePermission = $user->canEditProfileAndVitals();
                
                if ($role === 'dokter') {
                    $isNurse = $basePermission && $isAssignedDoctor;
                } else {
                    $isNurse = $basePermission;
                }

                $nurseClass = $isNurse ? 'bg-white focus:ring-yellow-400 border-gray-300' : 'bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200';
                $nursePlaceholder = $isNurse ? '...' : 'Area terbatas';
            @endphp

            <div class="pl-4 space-y-8">
                {{-- A. DATA PROFIL TAMBAHAN --}}
                <div>
                    <h4 class="text-xs font-black text-yellow-500 uppercase tracking-widest mb-4 border-b border-yellow-100 pb-2">1. Profil Kesehatan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Gol. Darah</label>
                            <input type="text" name="blood_type" value="{{ $profile->blood_type ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="{{ $nursePlaceholder }}" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Alergi</label>
                            <input type="text" name="allergies" value="{{ $profile->allergies ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="{{ $nursePlaceholder }}" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Nomor BPJS</label>
                            <input type="text" name="bpjs_number" value="{{ $profile->bpjs_number ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="{{ $nursePlaceholder }}" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Pekerjaan</label>
                            <input type="text" name="occupation" value="{{ $profile->occupation ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="{{ $nursePlaceholder }}" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>

                {{-- B. TANDA VITAL --}}
                <div>
                    <h4 class="text-xs font-black text-yellow-500 uppercase tracking-widest mb-4 border-b border-yellow-100 pb-2">2. Tanda Vital</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Tensi (mmHg)</label>
                            <input type="text" name="blood_pressure" value="{{ $mr->blood_pressure ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="120/80" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Suhu (°C)</label>
                            <input type="text" name="temperature" value="{{ $mr->temperature ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="36.5" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Berat (kg)</label>
                            <input type="text" name="weight" value="{{ $mr->weight ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="kg" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Tinggi (cm)</label>
                            <input type="text" name="height" value="{{ $mr->height ?? '' }}" 
                                class="w-full rounded-lg text-sm {{ $nurseClass }}" placeholder="cm" {{ !$isNurse ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$isNurse)
                <div class="absolute top-4 right-4 flex items-center gap-2 text-xs text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full border border-yellow-100">
                    <i class="fa-solid fa-user-nurse"></i> Read Only
                </div>
            @endif
        </div>


        {{-- ========================================================= --}}
        {{-- BAGIAN 3: DIAGNOSA & TINDAKAN (Hak Akses: ASSIGNED DOKTER) --}}
        {{-- ========================================================= --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-blue-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-2 h-full bg-[#5DB8D8]"></div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-6 pl-4 flex items-center gap-2">
                Diagnosa & Tindakan (Dokter)
            </h3>

            @php 
                // Logic Permission:
                // Hanya boleh edit jika Role Dokter DAN ID-nya sesuai dengan kunjungan
                $isDokter = ($role === 'dokter' && $isAssignedDoctor);

                $dokterClass = $isDokter ? 'bg-white focus:ring-blue-400 border-gray-300' : 'bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200';
                $dokterPlaceholder = $isDokter ? '...' : 'Area terbatas (Bukan Dokter Penanggung Jawab)';
            @endphp

            <div class="pl-4 space-y-4">
                {{-- Input Diagnosa --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosa (ICD-10)</label>
                        <input type="text" name="diagnosis_icd10" value="{{ $mr->diagnosis_icd10 ?? '' }}" 
                            class="w-full rounded-xl {{ $dokterClass }}" placeholder="{{ $dokterPlaceholder }}" {{ !$isDokter ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Rujukan (Opsional)</label>
                         <input type="text" name="refferal" value="{{ $mr->refferal ?? '' }}" 
                            class="w-full rounded-xl {{ $dokterClass }}" placeholder="{{ $dokterPlaceholder }}" {{ !$isDokter ? 'readonly' : '' }}>
                    </div>
                </div>

                {{-- Input Tindakan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tindakan / Prosedur Medis</label>
                    <textarea name="treatment_notes" rows="2" 
                        class="w-full rounded-xl {{ $dokterClass }}" placeholder="{{ $dokterPlaceholder }}" {{ !$isDokter ? 'readonly' : '' }}>{{ $mr->treatment_notes ?? '' }}</textarea>
                </div>

                {{-- Informed Consent --}}
                <div>
                    <label class="block text-xs font-bold text-blue-800 uppercase tracking-wide mb-2">Informed Consent</label>
                    <textarea name="consent" rows="4" 
                        class="w-full rounded-xl text-sm {{ $dokterClass }}" 
                        placeholder="Pasien telah dijelaskan mengenai risiko..." {{ !$isDokter ? 'readonly' : '' }}>{{ $visit->consent ?? '' }}</textarea>
                </div>

                {{-- Tindakan Lanjutan --}}
                <div>
                    <label class="block text-xs font-bold text-blue-800 uppercase tracking-wide mb-2">Tindakan Lanjutan</label>
                    <textarea name="follow_up_plan" rows="2" 
                        class="w-full rounded-xl text-sm {{ $dokterClass }}" 
                        placeholder="Instruksi kontrol..." {{ !$isDokter ? 'readonly' : '' }}>{{ $mr->follow_up_plan ?? '' }}</textarea>
                </div>

                {{-- Resep Obat (Dynamic) --}}
                <div class="col-span-1 md:col-span-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100"
                    x-data="medicationHandler({{ json_encode($mr->medications ?? []) }})">
                    
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-bold text-gray-700">Resep Obat & Aturan Pakai</label>
                        @if($isDokter)
                        <button type="button" @click="add()"
                            class="text-xs bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg font-bold hover:bg-blue-200 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Obat
                        </button>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <template x-for="(med, index) in medications" :key="index">
                            <div class="flex flex-col md:flex-row gap-2 items-start md:items-center">
                                <div class="flex-1 w-full">
                                    <input type="text" :name="'medications['+index+'][name]'" x-model="med.name"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-400 focus:border-blue-400 {{ !$isDokter ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                                        placeholder="Nama Obat" {{ !$isDokter ? 'readonly' : '' }} required>
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="text" :name="'medications['+index+'][dosage]'" x-model="med.dosage"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-400 focus:border-blue-400 {{ !$isDokter ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                                        placeholder="Dosis (3x1)" {{ !$isDokter ? 'readonly' : '' }} required>
                                </div>
                                @if($isDokter)
                                <button type="button" @click="remove(index)" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                @endif
                            </div>
                        </template>
                        <template x-if="medications.length === 0">
                            <div class="text-center py-4 border-2 border-dashed border-gray-200 rounded-lg text-gray-400 text-sm italic">
                                Belum ada resep obat.
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="medications" value="" x-if="medications.length === 0" disabled>
                </div>
            </div>

            @if(!$isDokter)
                <div class="absolute inset-0 bg-white/20 z-10 cursor-not-allowed" title="Area Khusus Dokter Penanggung Jawab"></div>
                <div class="absolute top-4 right-4 flex items-center gap-2 text-xs text-white bg-gray-400 px-3 py-1 rounded-full z-20">
                    <i class="fa-solid fa-lock"></i> Area Dokter
                </div>
            @endif
        </div>

        {{-- TOMBOL SIMPAN (Hanya muncul jika user punya hak edit di salah satu bagian) --}}
        @if($isAdmin || $isNurse || $isDokter)
        <div class="sticky bottom-4 z-30 flex justify-end">
            <button type="submit" class="bg-[#262C5B] hover:bg-[#1e234a] text-white font-bold py-4 px-10 rounded-full shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center gap-3">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Rekam Medis</span>
            </button>
        </div>
        @endif

    </form>
</div>

@push('scripts')
<script>
    function medicationHandler(existingData) {
        return {
            medications: Array.isArray(existingData) ? existingData : [],
            add() { this.medications.push({ name: '', dosage: '' }); },
            remove(index) { this.medications.splice(index, 1); }
        }
    }
</script>
@endpush
@endsection