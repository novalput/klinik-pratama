@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">
            Jadwal: {{ $date->translatedFormat('F Y') }}
        </h2>
        <div class="flex space-x-2">
            <a href="{{ route('dokter.schedule', ['month' => $prevMonth]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">
                &larr; Bulan Lalu
            </a>
            <a href="{{ route('dokter.schedule') }}" class="px-4 py-2 bg-blue-50 text-blue-700 rounded hover:bg-blue-100 text-sm font-medium border border-blue-200">
                Hari Ini
            </a>
            <a href="{{ route('dokter.schedule', ['month' => $nextMonth]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">
                Bulan Depan &rarr;
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border">
        
        <div class="grid grid-cols-7 bg-gray-50 border-b">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                <div class="py-3 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider border-r last:border-r-0">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 auto-rows-fr">
            
            {{-- 1. Render Kotak Kosong untuk hari sebelum tanggal 1 --}}
            @for($i = 1; $i < $startDayOfWeek; $i++)
                <div class="min-h-[120px] bg-gray-50 border-r border-b"></div>
            @endfor

            {{-- 2. Render Tanggal --}}
            @foreach($calendar as $day)
                <div class="min-h-[120px] border-r border-b relative group transition hover:bg-gray-50 
                    {{ $day['is_today'] ? 'bg-blue-50' : 'bg-white' }}">
                    
                    {{-- Angka Tanggal --}}
                    <div class="p-2 flex justify-between items-start">
                        <span class="text-sm font-bold {{ $day['is_today'] ? 'w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded-full' : 'text-gray-700' }}">
                            {{ $day['date'] }}
                        </span>
                        
                        {{-- Indikator Tipe (Dot kecil di pojok kanan atas) --}}
                        @if($day['type'] == 'exception-off')
                            <span class="w-2 h-2 rounded-full bg-red-500" title="Libur"></span>
                        @elseif($day['type'] == 'exception-on')
                            <span class="w-2 h-2 rounded-full bg-green-500" title="Tambahan"></span>
                        @elseif($day['type'] == 'routine')
                            <span class="w-2 h-2 rounded-full bg-blue-400" title="Rutin"></span>
                        @endif
                    </div>

                    {{-- Konten Jadwal --}}
                    <div class="px-2 pb-2 text-xs">
                        @if($day['type'] == 'empty')
                            <span class="text-gray-300 italic">-</span>
                        @elseif($day['type'] == 'exception-off')
                            <div class="bg-red-100 text-red-700 p-1 rounded font-medium text-center border border-red-200 mt-1">
                                LIBUR
                            </div>
                        @elseif($day['type'] == 'exception-on')
                            <div class="bg-green-100 text-green-700 p-1 rounded border border-green-200 mt-1">
                                <div class="font-bold">Tambahan</div>
                                <div>{{ $day['time'] }}</div>
                            </div>
                        @elseif($day['type'] == 'routine')
                            <div class="bg-blue-100 text-blue-700 p-1 rounded border border-blue-200 mt-1">
                                <div class="font-bold">Praktik</div>
                                <div>{{ $day['time'] }}</div>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach

            {{-- 3. (Opsional) Kotak kosong sisa jika bulan berakhir sebelum Minggu, biar rapi --}}
            @php
                $totalCells = ($startDayOfWeek - 1) + count($calendar);
                $remainingCells = 7 - ($totalCells % 7);
            @endphp
            @if($remainingCells < 7)
                @for($i = 0; $i < $remainingCells; $i++)
                    <div class="min-h-[120px] bg-gray-50 border-r border-b"></div>
                @endfor
            @endif

        </div>
    </div>
    
    <div class="mt-6 flex flex-wrap gap-4 text-sm text-gray-600">
        <div class="flex items-center">
            <span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span> Jadwal Rutin
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span> Jadwal Tambahan
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> Libur / Cuti
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-blue-600 rounded-full mr-2"></span> Hari Ini
        </div>
    </div>

</div>
@endsection