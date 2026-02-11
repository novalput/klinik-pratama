@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-[#262C5B] mb-6">Pendaftaran Kunjungan Baru</h2>
        
        <form action="{{ route('visits.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Pasien</label>
                    <select name="patient_id" class="w-full rounded-xl border-gray-300 focus:border-[#5DB8D8] focus:ring focus:ring-sky-200 transition">
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->no_rm }} - {{ $p->name }}</option>
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