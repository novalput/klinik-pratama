@extends('layouts.app')

@section('title', 'Kelola Tenaga Kesehatan')

@section('content')
<div x-data="nakesCrud()" class="w-full">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Kelola Tenaga Kesehatan</h1>
            <p class="text-gray-500 text-sm">Manajemen data nakes</p>
        </div>
        <button @click="openCreate()"
            class="px-6 py-2 bg-[#4da4e3] text-white rounded-lg font-semibold hover:bg-[#367cae]">
            <i class="fas fa-plus mr-1"></i> Tambah Nakes
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow border overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-[#4da4e3] text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">No HP</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($nakes as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->email }}</td>
                    <td class="px-6 py-4">{{ $item->phone }}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button @click='openEdit({
                            id: {{ $item->id }},
                            name: "{{ $item->name }}",
                            email: "{{ $item->email }}",
                            phone: "{{ $item->phone }}",
                            nip: "{{ $item->nakes->nip }}",
                            education: "{{ $item->nakes->education }}",
                            specialization: "{{ $item->nakes->specialization }}",
                            position: "{{ $item->nakes->position }}",
                            bio: "{{ $item->nakes->bio }}",
                            status: "{{ $item->nakes->status }}"
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
                        x-text="formMode === 'create' ? 'Tambah Tenaga Kesehatan' : 'Edit Tenaga Kesehatan'"></h2>
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

                    {{-- Email --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <div class="relative mt-1">
                            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input x-model="form.email" type="email"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        </div>
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

                    {{-- NIP --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">NIP</label>
                        <div class="relative mt-1">
                            <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input x-model="form.nip" required
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                        </div>
                    </div>

                    {{-- Password (Create Only) --}}
                    <template x-if="formMode === 'create'">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-600">Password</label>
                            <div class="relative mt-1">
                                <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="password" x-model="form.password" required
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                            </div>
                        </div>
                    </template>

                    {{-- Pendidikan --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Pendidikan</label>
                        <input x-model="form.education"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div>

                    {{-- Spesialisasi --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Spesialisasi</label>
                        <input x-model="form.specialization"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div>
                    
                    {{-- Posisi --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Posisi</label>
                        <input x-model="form.position"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]">
                    </div>
                </div>

                {{-- Bio --}}
                <div>
                    <label class="text-sm font-medium text-gray-600">Bio</label>
                    <textarea x-model="form.bio" rows="3"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#4da4e3]"></textarea>
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
            <p class="text-gray-600 mb-6">Data nakes akan dihapus permanen.</p>
            <div class="flex justify-end gap-3">
                <button @click="modalDelete = false" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button @click="deleteData" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function nakesCrud() {
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
                position: '',
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
                    position: '',
                    bio: '',
                    status: '',
                };
            },

            async submitForm() {
                this.errorMessage = '';
                try {
                    let url = this.formMode === 'create' ?
                        "{{ route('admin.nakes.store') }}" :
                        `/admin/nakes/${this.form.id}`;
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
                await fetch(`/admin/nakes/${this.deleteId}`, {
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