@extends('layouts.app')

@section('content')
{{-- 
    UPDATE 1: Kirim data Auth User (Role & ID Dokter) ke AlpineJS 
--}}
@php
    // Siapkan data user untuk JS
    $currentUser = [
        'role' => Auth::user()->role,
        // Jika dokter, ambil ID tabel dokternya. Jika bukan, null.
        'dokter_id' => Auth::user()->role == 'dokter' && Auth::user()->dokter ? Auth::user()->dokter->id : null
    ];
@endphp

<div class="max-w-6xl mx-auto p-4 md:p-8 space-y-6" 
    x-data="{ 
        ...medicalRecordsHandler({{ json_encode($visits) }}, {{ json_encode($currentUser) }}),
        exportModalOpen: false,
        selectedExportIds: [] 
    }">

    {{-- LOGIC: Tentukan Route Kembali berdasarkan Role (Tetap Sama) --}}
    @php
        $role = Auth::user()->role;
        $backRoute = '#';

        if ($role === 'dokter') {
            $backRoute = route('dokter.datapasien');
        } elseif ($role === 'nakes') {
            $backRoute = route('nakes.datapasien');
        } elseif ($role === 'staf_administrasi') {
            $backRoute = route('staff_administrasi.datapasien.index');
        } elseif ($role === 'admin') {
            $backRoute = route('admin.patient.index');
        }
    @endphp

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <div>
                <span class="font-bold block">Berhasil!</span>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
            {{-- Tombol Close (Opsional) --}}
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    {{-- HEADER & NAVIGASI (Tetap Sama) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <a href="{{ $backRoute }}"
               class="text-gray-400 hover:text-gray-600 mb-2 inline-flex items-center group transition-colors">
                <i class="fa-solid fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> 
                Kembali ke Data Pasien
            </a>
            <h1 class="text-3xl font-bold text-[#262C5B]">{{ $patient->name }}</h1>
            <p class="text-gray-500 text-sm">
                No. RM: <span class="font-mono font-bold">{{ $patient->no_rm ?? '-' }}</span> | 
                NIK: {{ $patient->nik }}
            </p>
        </div>

        <div class="flex gap-2">
            {{-- TOMBOL EXPORT (BARU) --}}
            <button @click="exportModalOpen = true; selectedExportIds = visits.map(v => v.id)" 
                class="bg-white text-gray-700 border border-gray-300 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i> 
                <span class="hidden md:inline">Cetak Rekam Medis</span>
            </button>

            {{-- Tombol Tambah Kunjungan --}}
            @if(Auth::user()->canEditAdministrasiData())
            <a href="{{ route('visits.create') }}?patient_id={{ $patient->id }}" 
               class="bg-[#5DB8D8] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#4A9FBD] shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kunjungan
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI (Tetap Sama) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Data Diri Pasien</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase">Tanggal Lahir</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($patient->dob)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase">Alamat</span>
                        <span class="font-semibold">{{ $patient->address }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase">Gol. Darah</span>
                        <span class="font-semibold">{{ $patient->patientProfile->blood_type ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-[#262C5B] text-white rounded-[2rem] p-6 shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <label class="block text-sky-300 text-xs font-bold uppercase tracking-widest mb-2">
                        Pilih Riwayat Kunjungan
                    </label>
                    <div class="relative">
                        <select x-model="selectedId" 
                                class="w-full appearance-none bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 pr-8 focus:outline-none focus:ring-2 focus:ring-sky-400 font-bold cursor-pointer hover:bg-white/20 transition-all">
                            <option value="" class="text-gray-800">-- Pilih Tanggal Kunjungan --</option>
                            <template x-for="visit in visits" :key="visit.id">
                                <option :value="visit.id" 
                                        class="text-gray-800"
                                        x-text="formatDate(visit.visit_date) + ' - ' + (visit.medical_record.diagnosis_icd10 || 'Belum Diagnosa')">
                                </option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-white">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-white/60 italic">
                        *Pilih salah satu tanggal di atas untuk melihat detail lengkap rekam medis di sebelah kanan.
                    </p>
                    <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                        <span class="text-xs font-bold">Total Kunjungan:</span>
                        <span class="bg-sky-500 px-2 py-1 rounded text-xs font-bold" x-text="visits.length"></span>
                    </div>
                </div>
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            </div>
        </div>

        {{-- KOLOM KANAN (DETAIL) --}}
        <div class="lg:col-span-2">
            
            <div x-show="!selectedId" 
                 class="h-full min-h-[400px] flex flex-col items-center justify-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200 text-center p-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-medical text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-400">Belum ada kunjungan dipilih</h3>
                <p class="text-gray-400 mt-2 max-w-sm">Silakan pilih tanggal kunjungan pada dropdown di sebelah kiri untuk melihat detail rekam medis.</p>
            </div>

            <template x-for="visit in visits" :key="visit.id">
                <div x-show="selectedId == visit.id" 
                     x-transition:enter="transition ease-out duration-300"
                     class="bg-white rounded-[2.5rem] shadow-lg border border-gray-100 overflow-hidden">
                    
                    {{-- Header Detail --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white flex justify-between items-center">
                        <div>
                            <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-1">Tanggal Kunjungan</p>
                            <h2 class="text-2xl font-bold" x-text="formatDateFull(visit.visit_date)"></h2>
                            <div class="mt-2 flex items-center gap-2 text-xs text-blue-200">
                                <i class="fa-solid fa-user-doctor"></i>
                                <span>
                                    Diperiksa oleh: 
                                    <span class="font-bold text-white text-sm" 
                                          x-text="visit.dokter && visit.dokter.user ? visit.dokter.user.name : 'Dokter Tidak Diketahui'">
                                    </span>
                                </span>
                            </div>
                        </div>

                        {{-- 
                            UPDATE 2: LOGIKA TOMBOL EDIT/LIHAT 
                            Menggunakan fungsi checkAccess(visit) dari JS
                        --}}
                        <template x-if="checkAccess(visit)">
                            {{-- Jika Berhak Edit --}}
                            <a :href="'/visits/' + visit.id + '/edit'" 
                               class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all backdrop-blur-sm">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit / Lengkapi
                            </a>
                        </template>

                        <template x-if="!checkAccess(visit)">
                            {{-- Jika Tidak Berhak Edit (Lihat Saja) --}}
                            <a :href="'/visits/' + visit.id + '/edit'" 
                               class="bg-gray-800/40 hover:bg-gray-800/60 text-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition-all backdrop-blur-sm flex items-center">
                                <i class="fa-regular fa-eye mr-1"></i> Lihat Detail
                            </a>
                        </template>

                    </div>

                    <div class="p-8 space-y-8">
                        {{-- 1. Keluhan & Awal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Keluhan Utama</h4>
                                <p class="text-gray-800 font-medium" x-text="visit.complaint || '-'"></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jenis Pembayaran</h4>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase"
                                      :class="visit.payment_type === 'bpjs' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'"
                                      x-text="visit.payment_type"></span>
                            </div>
                        </div>

                        {{-- 2. Tanda Vital --}}
                        <div>
                            <h4 class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-4 pb-2 border-b">
                                <i class="fa-solid fa-heart-pulse text-red-400"></i> Tanda Vital
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-data="{ mr: visit.medical_record || {} }">
                                <div class="text-center p-3 bg-red-50 rounded-xl">
                                    <span class="block text-xs text-gray-500">Tensi</span>
                                    <span class="font-bold text-gray-800" x-text="mr.blood_pressure || '-'"></span>
                                </div>
                                <div class="text-center p-3 bg-orange-50 rounded-xl">
                                    <span class="block text-xs text-gray-500">Suhu</span>
                                    <span class="font-bold text-gray-800" x-text="mr.temperature ? mr.temperature + '°C' : '-'"></span>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-xl">
                                    <span class="block text-xs text-gray-500">Berat</span>
                                    <span class="font-bold text-gray-800" x-text="mr.weight ? mr.weight + ' kg' : '-'"></span>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-xl">
                                    <span class="block text-xs text-gray-500">Tinggi</span>
                                    <span class="font-bold text-gray-800" x-text="mr.height ? mr.height + ' cm' : '-'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Diagnosa & Tindakan --}}
                        <div>
                            <h4 class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-4 pb-2 border-b">
                                <i class="fa-solid fa-user-doctor text-blue-500"></i> Diagnosa & Tindakan
                            </h4>
                            <div class="space-y-4" x-data="{ mr: visit.medical_record || {} }">
                                <div>
                                    <span class="text-xs font-bold text-gray-400 uppercase">Diagnosa (ICD-10)</span>
                                    <p class="text-lg font-bold text-[#262C5B]" x-text="mr.diagnosis_icd10 || 'Belum diisi dokter'"></p>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-gray-400 uppercase">Tindakan / Prosedur</span>
                                    <p class="text-gray-700 bg-blue-50/50 p-4 rounded-xl mt-1 border border-blue-100" 
                                       x-text="mr.treatment_notes || '-'"></p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs font-bold text-gray-400 uppercase">Resep Obat</span>
                                        <ul class="list-disc list-inside text-sm text-gray-700 mt-1">
                                            <template x-if="mr.medications">
                                                <template x-for="med in JSON.parse(mr.medications)" :key="med">
                                                    <li x-text="med.name + ' (' + med.dosage + ')'"></li>
                                                </template>
                                            </template>
                                            <template x-if="!mr.medications">
                                                <li class="list-none text-gray-400 italic">-</li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-gray-400 uppercase">Rujukan & Follow Up</span>
                                        <p class="text-sm text-gray-700 mt-1" x-text="mr.follow_up_plan || '-'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============================================= --}}
        {{-- MODAL EXPORT PDF (LETAKKAN DI PALING BAWAH SEBELUM ENDSECTION) --}}
        {{-- ============================================= --}}
        <template x-if="exportModalOpen">
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="exportModalOpen = false"></div>
                
                {{-- Modal Content --}}
                <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 text-lg">Cetak Rekam Medis</h3>
                        <button @click="exportModalOpen = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('export.medical_record') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        
                        <div class="p-6 max-h-[60vh] overflow-y-auto">
                            <p class="text-sm text-gray-500 mb-4">Pilih kunjungan yang ingin dicetak ke dalam PDF:</p>
                            
                            {{-- Select All Toggle --}}
                            <div class="flex items-center mb-3 pb-3 border-b border-gray-100">
                                <input type="checkbox" id="selectAll" 
                                    {{-- LOGIKA 1: Otomatis Checked jika jumlah ID yang dipilih SAMA DENGAN jumlah total Kunjungan --}}
                                    :checked="visits.length > 0 && selectedExportIds.length === visits.length"
                                    
                                    {{-- LOGIKA 2 (Opsional): Tampilkan tanda 'strip' (-) jika baru terpilih sebagian --}}
                                    :indeterminate="selectedExportIds.length > 0 && selectedExportIds.length < visits.length"

                                    {{-- LOGIKA 3: Saat diklik, lakukan Select All atau Deselect All --}}
                                    @change="$el.checked ? selectedExportIds = visits.map(v => v.id) : selectedExportIds = []"
                                    
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                
                                <label for="selectAll" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                    Pilih Semua 
                                    {{-- Menampilkan counter (opsional) --}}
                                    <span class="text-xs font-normal text-gray-500" x-text="'(' + selectedExportIds.length + '/' + visits.length + ')'"></span>
                                </label>
                            </div>

                            {{-- List Kunjungan Checkbox --}}
                            <div class="space-y-2">
                                <template x-for="visit in visits" :key="visit.id">
                                    <label class="flex items-start p-3 rounded-lg border border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="visit_ids[]" :value="visit.id" x-model="selectedExportIds"
                                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-800" x-text="formatDateFull(visit.visit_date)"></span>
                                            <span class="block text-xs text-gray-500" x-text="visit.medical_record.diagnosis_icd10 || 'Belum Diagnosa'"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button type="button" @click="exportModalOpen = false" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit" 
                                :disabled="selectedExportIds.length === 0"
                                class="px-4 py-2 bg-[#262C5B] text-white rounded-lg text-sm font-bold hover:bg-[#1e234a] shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <i class="fa-solid fa-file-pdf"></i> Download PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- 
    UPDATE 3: SCRIPT LOGIKA OTORISASI DI CLIENT SIDE
--}}
<script>
    function medicalRecordsHandler(visitsData, currentUser) {
        return {
            visits: visitsData,
            currentUser: currentUser, // Data user yang login
            selectedId: '', 

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            formatDateFull(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            // FUNGSI UTAMA CEK AKSES
            checkAccess(visit) {
                const role = this.currentUser.role;
                
                // 1. Admin & Staf Administrasi: Boleh Edit Semua
                if (role === 'admin' || role === 'staf_administrasi') {
                    return true;
                }

                // 2. Nakes (Perawat): Boleh Edit Semua (Untuk isi vital)
                if (role === 'nakes') {
                    return true;
                }

                // 3. Dokter: Hanya boleh edit jika ID Dokter di kunjungan == ID Dokter Login
                if (role === 'dokter') {
                    // Cek apakah visit.dokter_id ada dan sama dengan currentUser.dokter_id
                    if (visit.dokter_id && visit.dokter_id == this.currentUser.dokter_id) {
                        return true;
                    }
                    // Jika dokter tujuan kosong, mungkin boleh diklaim (opsional, return true jika mau)
                    // Tapi defaultnya false agar dokter lain tidak asal edit
                    return false; 
                }

                return false;
            }
        }
    }
</script>
@endsection