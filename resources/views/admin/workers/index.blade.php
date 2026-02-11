@extends('layouts.app')

@section('title', 'Kelola Karyawan')

@section('content')
<div x-data="karyawanCrud()" class="w-full">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Kelola Karyawan</h1>
            <p class="text-gray-500 text-sm">Manajemen data karyawan</p>
        </div>
        <button @click="openCreate()"
            class="px-6 py-2 bg-[#4da4e3] text-white rounded-lg font-semibold hover:bg-[#367cae]">
            <i class="fas fa-plus mr-1"></i> Tambah Karyawan
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow border overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-[#4da4e3] text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">NIP</th>
                    <th class="px-6 py-3 text-left">No HP</th>
                    <th class="px-6 py-3 text-left">Posisi</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($worker as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->nip }}</td>
                    <td class="px-6 py-4">{{ $item->phone }}</td>
                    <td class="px-6 py-4">{{ $item->position }}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button @click='openEdit({
                            id: {{ $item->id }},
                            name: "{{ $item->name }}",
                            nip: "{{ $item->nip }}",
                            position: "{{ $item->position }}",
                            phone: "{{ $item->phone }}",
                            address: "{{ $item->address }}",
                            status: "{{ $item->status }}"
                        })'
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="openDelete({{ $item->id }})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= MODAL FORM ================= --}}
    <div x-show="modalForm" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>

        {{-- Modal --}}
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-full sm:max-w-lg md:max-w-2xl mx-2 sm:mx-4">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-[#4da4e3] to-[#367cae] text-white">
                <div class="flex items-center gap-3">
                    <i class="fas fa-user-doctor text-xl"></i>
                    <h2 class="text-lg font-semibold"
                        x-text="formMode === 'create' ? 'Tambah Karyawan' : 'Edit Karyawan'"></h2>
                </div>
                <button @click="close()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Body --}}
            <form @submit.prevent="submitForm" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama</label>
                        <div class="relative mt-1">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input x-model="form.name" required
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        </div>
                    </div>

                    {{-- NIP --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">NIP</label>
                        <div class="relative mt-1">
                            <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input x-model="form.nip" required
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        </div>
                    </div>
                    
                    {{-- Posisi --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Posisi</label>
                        <input x-model="form.position"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">No HP</label>
                        <div class="relative mt-1">
                            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input x-model="form.phone"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Alamat</label>
                        <input x-model="form.address"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div>

                    {{-- Status --}}
                    <!-- <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <input x-model="form.status"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div> -->
                </div>

                {{-- Status --}}
                <div>
                    <label class="text-sm font-medium text-gray-600">Status</label>
                    <select x-model="form.status"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        <option value="">-- Pilih Status --</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                {{-- Error --}}
                <p x-show="errorMessage" x-text="errorMessage" class="text-sm text-red-600"></p>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" @click="close()"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2 rounded-lg bg-[#4da4e3] hover:bg-[#367cae] text-white font-semibold">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= DELETE MODAL ================= --}}
    <div x-show="modalDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="modalDelete = false"></div>

        {{-- Modal --}}
        <div class="relative z-10 bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-3 text-gray-800">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">Data karyawan akan dihapus permanen.</p>
            <div class="flex justify-end gap-3">
                <button @click="modalDelete = false" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button @click="deleteData" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function karyawanCrud() {
        return {
            modalForm: false,
            modalDelete: false,
            formMode: 'create',
            deleteId: null,
            errorMessage: '',
            form: {
                id: null,
                name: '',
                email: '',
                phone: '',
                nip: '',
                password: '',
                education: '',
                specialization: '',
                bio: '',
                status: '',
            },

            openCreate() {
                this.formMode = 'create';
                this.reset();
                this.modalForm = true;
            },

            openEdit(data) {
                this.formMode = 'edit';
                this.form = {
                    ...data,
                    password: ''
                };
                this.modalForm = true;
            },

            openDelete(id) {
                this.deleteId = id;
                this.modalDelete = true;
            },

            close() {
                this.modalForm = false;
                this.errorMessage = '';
            },

            reset() {
                this.form = {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    nip: '',
                    password: '',
                    education: '',
                    specialization: '',
                    bio: '',
                    status: '',
                };
            },

            async submitForm() {
                this.errorMessage = '';
                try {
                    let url = this.formMode === 'create' ?
                        "{{ route('admin.workers.storeWorkers') }}" :
                        `/admin/workers/${this.form.id}`;
                    let method = this.formMode === 'create' ? 'POST' : 'PUT';

                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch {
                        data = {
                            message: 'Terjadi kesalahan server'
                        };
                    }

                    if (!res.ok) {
                        // Ambil error dari validation atau message
                        this.errorMessage = data?.errors ?
                            Object.values(data.errors).flat()[0] :
                            data?.message || 'Terjadi kesalahan';
                        return;
                    }

                    // Berhasil
                    location.reload();
                } catch (err) {
                    this.errorMessage = 'Terjadi kesalahan jaringan';
                    console.error(err);
                }
            },

            async deleteData() {
                await fetch(`/admin/workers/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                location.reload();
            }
        }
    }
</script>
@endsection
