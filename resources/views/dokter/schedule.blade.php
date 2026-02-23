@extends('layouts.app') 

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Jadwal: {{ $dokter->user->name }}</h2>
        <a href="{{ route('admin.dokter.index') }}" class="text-blue-500 hover:underline">&larr; Kembali</a>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error Validasi --}}
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong class="font-bold">Gagal!</strong>
        <ul class="list-disc ml-5 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- KOLOM 1: JADWAL RUTIN (Templates) --}}
        <div class="bg-white p-6 rounded-lg shadow h-fit">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">📅 Jadwal Rutin Mingguan</h3>
            
            {{-- List Jadwal Rutin --}}
            <ul class="space-y-2 mb-6">
                @forelse($dokter->scheduleTemplates as $template)
                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded border">
                        <div>
                            <span class="font-bold text-blue-600 w-20 inline-block">{{ $template->day }}</span>
                            <span>{{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}</span>
                        </div>
                        <form action="{{ route('admin.dokter.schedule.template.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Hapus</button>
                        </form>
                    </li>
                @empty
                    <p class="text-gray-500 text-sm italic">Belum ada jadwal rutin.</p>
                @endforelse
            </ul>

            {{-- Form Tambah Jadwal Rutin --}}
            <form action="{{ route('admin.dokter.schedule.template.store', $dokter->id) }}" method="POST" class="bg-gray-50 p-4 rounded border">
                @csrf
                <h4 class="font-medium mb-2 text-sm text-gray-700">Tambah Jadwal Rutin</h4>
                <div class="mb-3">
                    <select name="day" class="border p-2 rounded text-sm w-full" required>
                        <option value="">Pilih Hari</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                            <option value="{{ $hari }}">{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="text-xs text-gray-500">Jam Mulai</label>
                        <input type="time" name="start_time" class="border p-2 rounded text-sm w-full" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Jam Selesai</label>
                        <input type="time" name="end_time" class="border p-2 rounded text-sm w-full" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 text-sm font-semibold">Simpan Jadwal</button>
            </form>
        </div>

        {{-- KOLOM 2: PENGECUALIAN (Exceptions) --}}
        <div class="bg-white p-6 rounded-lg shadow h-fit">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">⚠️ Pengecualian / Cuti</h3>

            {{-- List Exception --}}
            <ul class="space-y-2 mb-6">
                @forelse($dokter->scheduleExceptions as $exc)
                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded border {{ $exc->type == 'libur' ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }}">
                        <div>
                            <div class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($exc->date)->format('d M Y') }}</div>
                            <div class="text-sm">
                                @if($exc->type == 'libur')
                                    <span class="text-red-600 font-bold">LIBUR / CUTI</span>
                                @else
                                    <span class="text-green-600 font-bold">JADWAL TAMBAHAN</span>
                                    <br>
                                    <span class="text-xs">({{ \Carbon\Carbon::parse($exc->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($exc->end_time)->format('H:i') }})</span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('admin.dokter.schedule.exception.destroy', $exc->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 font-bold px-2">&times;</button>
                        </form>
                    </li>
                @empty
                    <p class="text-gray-500 text-sm italic">Tidak ada data pengecualian mendatang.</p>
                @endforelse
            </ul>

            {{-- Form Tambah Pengecualian --}}
            <form action="{{ route('admin.dokter.schedule.exception.store', $dokter->id) }}" method="POST" class="bg-gray-50 p-4 rounded border" x-data="{ type: 'libur' }">
                @csrf
                <h4 class="font-medium mb-2 text-sm text-gray-700">Tambah Pengecualian</h4>
                
                <div class="mb-3">
                    <label class="text-xs text-gray-500">Tanggal</label>
                    <input type="date" name="date" class="border p-2 rounded text-sm w-full" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-3">
                    <label class="text-xs text-gray-500">Jenis</label>
                    <select name="type" x-model="type" class="border p-2 rounded text-sm w-full" required>
                        <option value="libur">Libur / Cuti</option>
                        <option value="tambahan">Jadwal Tambahan</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3" x-show="type === 'tambahan'" style="display: none;">
                    <div>
                        <label class="text-xs text-gray-500">Mulai</label>
                        <input type="time" name="start_time" class="border p-2 rounded text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Selesai</label>
                        <input type="time" name="end_time" class="border p-2 rounded text-sm w-full">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-900 text-sm font-semibold">Simpan Pengecualian</button>
            </form>
        </div>
    </div>
</div>

{{-- AlpineJS wajib ada untuk logika x-show --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection