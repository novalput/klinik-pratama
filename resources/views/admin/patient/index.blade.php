@extends('layouts.app')

@section('title', 'Data Pasien & Rekam Medis')

@section('content')
<div class="p-4 md:p-8" x-data="patientManager()">
    
    {{-- Header & Tombol Tambah --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#262C5B]">Data Pasien & Rekam Medis</h1>
            <p class="text-gray-500 text-sm">Kelola biodata pasien dan akses riwayat pemeriksaan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Search Bar --}}
            <div class="relative group flex-1 md:flex-none">
                <input type="text" x-model="searchQuery" @keyup.enter="filterTable()" placeholder="Cari Nama / No RM..."
                    class="w-full md:w-64 pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8] text-sm outline-none shadow-sm transition-all">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#5DB8D8]"></i>
            </div>

            {{-- TOMBOL TAMBAH PASIEN --}}
            @if(Auth::user()->canEditAdministrasiData())
            <button @click="openModal('add')"
                class="flex items-center gap-2 bg-[#262C5B] hover:bg-[#1e234a] text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fa-solid fa-user-plus"></i>
                <span>Pasien Baru</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl shadow-sm animate-in fade-in slide-in-from-top-2">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- TABEL PASIEN --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-5 font-black">Identitas Pasien</th>
                        <th class="px-6 py-5 font-black text-center">Usia & Gender</th>
                        <th class="px-6 py-5 font-black text-right">Aksi & Rekam Medis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        
                        {{-- Kolom 1: Identitas --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-[#5DB8D8]/10 text-[#5DB8D8] flex items-center justify-center text-sm font-bold">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-base">{{ $patient->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        RM: {{ $patient->no_rm ?? 'Blm Ada' }} | NIK: {{ $patient->nik }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fa-brands fa-whatsapp text-green-500 mr-1"></i> 
                                        {{ $patient->phone ? '+62' . substr($patient->phone, 1) : '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom 2: Usia & Gender --}}
                        <td class="px-6 py-4 text-center">
                            <span class="block font-bold text-gray-700">
                                {{ \Carbon\Carbon::parse($patient->dob)->age }} Tahun
                            </span>
                            <span class="text-xs text-gray-400 capitalize">
                                {{ $patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>

                        {{-- Kolom 3: AKSI UTAMA --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                
                                {{-- Tombol Edit Biodata (Kecil) --}}
                                @if(Auth::user()->canEditAdministrasiData())
                                <button @click="openModal('edit', {{ json_encode($patient) }}, '{{ $patient->name }}', '{{ $patient->phone }}')"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="Edit Biodata">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @endif

                                {{-- Tombol UTAMA: Buka Rekam Medis (Show Page) --}}
                                @php
                                    $showRoute = '#';
                                    if(Auth::user()->role === 'dokter') {
                                        $showRoute = route('dokter.monitoring.show', $patient->id);
                                    } elseif(Auth::user()->role === 'staf_administrasi') {
                                        $showRoute = route('staff_administrasi.monitoring.show', $patient->id);
                                    } elseif(Auth::user()->role === 'admin') {
                                        $showRoute = route('admin.monitoring.show', $patient->id);
                                    } else {
                                        $showRoute = route('nakes.monitoring.show', $patient->id);
                                    }
                                @endphp
                                <a href="{{ $showRoute }}" 
                                   class="flex items-center gap-2 bg-[#5DB8D8] hover:bg-[#4A9FBD] text-white px-4 py-2 rounded-xl font-bold text-sm shadow-md shadow-sky-100 transition-all hover:scale-105 active:scale-95">
                                    <span>Buka Rekam Medis</span>
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-30"></i>
                                <p class="text-sm">Belum ada data pasien.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="p-6 border-t border-gray-50">
            {{ $patients->links() }}
        </div>
    </div>

    {{-- =========================== --}}
    {{-- MODAL FORM (Alpine JS) --}}
    {{-- =========================== --}}
    <template x-if="isOpen">
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
            
            {{-- Content --}}
            <div class="relative bg-white w-full max-w-2xl rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-xl font-bold text-[#262C5B]" x-text="mode === 'edit' ? 'Edit Biodata Pasien' : 'Registrasi Pasien Baru'"></h2>
                    <button @click="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-8 max-h-[80vh] overflow-y-auto no-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama & HP --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" x-model="formData.name" required class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8] bg-gray-50/30">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">WhatsApp</label>
                            <input type="text" x-model="formData.phone" @input="if(!formData.phone.startsWith('+62')) formData.phone = '+62'" required placeholder="+62..." class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8] bg-gray-50/30">
                        </div>

                        <div class="md:col-span-2 border-t border-dashed border-gray-200 my-2"></div>

                        {{-- Identitas --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">NIK (KTP)</label>
                            <input type="text" x-model="formData.nik" required maxlength="16" class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tgl Lahir</label>
                            <input type="date" x-model="formData.dob" required class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gender</label>
                            <select x-model="formData.gender" class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8]">
                                <option value="female">Perempuan</option>
                                <option value="male">Laki-laki</option>
                            </select>
                        </div>
                        
                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                            <textarea x-model="formData.address" rows="2" class="w-full rounded-xl border-gray-200 focus:border-[#5DB8D8] focus:ring-[#5DB8D8]"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4 pt-6 border-t border-gray-100">
                        <button type="button" @click="closeModal()" class="flex-1 py-3.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3.5 bg-[#262C5B] text-white font-bold rounded-xl hover:bg-[#1e234a] shadow-xl transition-all flex items-center justify-center gap-2">
                            <span x-show="!loading" x-text="mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Data Pasien'"></span>
                            <i x-show="loading" class="fas fa-circle-notch animate-spin"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    
</div>

{{-- SCRIPT ALPINE JS --}}
@push('scripts')
<script>
    function patientManager() {
        return {
            isOpen: false,
            mode: 'add',
            loading: false,
            searchQuery: '{{ request('search') }}',
            editingId: null,
            formData: {
                name: '', phone: '', nik: '', dob: '', gender: '', address: ''
            },

            openModal(mode, data = null, name = '', phone = '') {
                this.mode = mode;
                this.isOpen = true;
                if (mode === 'edit' && data) {
                    this.editingId = data.id;
                    let phoneForUi = phone.startsWith('0') ? '+62' + phone.substring(1) : phone;
                    
                    this.formData = {
                        name: name,
                        phone: phoneForUi,
                        nik: data.nik,
                        dob: data.dob ? data.dob.split('T')[0] : '',
                        gender: data.gender,
                        address: data.address
                    };
                } else {
                    this.resetForm();
                }
            },

            closeModal() {
                this.isOpen = false;
            },

            resetForm() {
                this.formData = {
                    name: '', phone: '+62', nik: '', dob: '', gender: 'female', address: ''
                };
                this.editingId = null;
            },

            async submitForm() {
                this.loading = true;
                // Pastikan route ini sesuai dengan role yang login
                const baseUrl = "{{ Auth::user()->role == 'staf_administrasi' ? route('staff_administrasi.datapasien.store') : route('nakes.datapasien.store') }}";
                
                // Gunakan URL yang benar untuk Update
                let updateUrl = baseUrl;
                if(this.mode === 'edit') {
                     updateUrl = "{{ Auth::user()->role == 'staf_administrasi' ? route('staff_administrasi.datapasien.update', ':id') : route('nakes.datapasien.update', ':id') }}";
                     updateUrl = updateUrl.replace(':id', this.editingId);
                }
                
                try {
                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ...this.formData,
                            _method: this.mode === 'edit' ? 'PUT' : 'POST'
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert(result.message || 'Terjadi kesalahan validasi');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal terhubung ke server.');
                } finally {
                    this.loading = false;
                }
            },

            filterTable() {
                let url = new URL(window.location.href);
                if (this.searchQuery.trim() !== '') {
                    url.searchParams.set('search', this.searchQuery);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }
        }
    }
</script>
@endpush
@endsection