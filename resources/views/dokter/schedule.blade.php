@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Jadwal Praktik</h2>
            <p class="text-gray-500">{{ $date->translatedFormat('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dokter.schedule', ['month' => $prevMonth]) }}"
               class="rounded border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                &larr; Bulan sebelumnya
            </a>
            <a href="{{ route('dokter.schedule', ['month' => $nextMonth]) }}"
               class="rounded border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                Bulan berikutnya &rarr;
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="grid grid-cols-7 border-b bg-gray-50 text-center text-sm font-semibold text-gray-600">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $dayName)
                <div class="p-3">{{ $dayName }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7">
            @for($blank = 1; $blank < $startDayOfWeek; $blank++)
                <div class="min-h-32 border-b border-r bg-gray-50"></div>
            @endfor

            @foreach($calendar as $day)
                <div class="min-h-32 border-b border-r p-3 {{ $day['is_today'] ? 'bg-blue-50' : '' }}">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="font-semibold {{ $day['is_today'] ? 'text-blue-600' : 'text-gray-700' }}">
                            {{ $day['date'] }}
                        </span>
                        @if($day['is_today'])
                            <span class="text-xs font-medium text-blue-600">Hari ini</span>
                        @endif
                    </div>

                    @if($day['type'] === 'routine')
                        <div class="rounded bg-green-100 px-2 py-1 text-xs text-green-700">
                            <div class="font-semibold">{{ $day['status'] }}</div>
                            <div>{{ $day['time'] }}</div>
                        </div>
                    @elseif($day['type'] === 'exception-on')
                        <div class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-700">
                            <div class="font-semibold">{{ $day['status'] }}</div>
                            <div>{{ $day['time'] }}</div>
                        </div>
                    @elseif($day['type'] === 'exception-off')
                        <div class="rounded bg-red-100 px-2 py-1 text-xs text-red-700">
                            {{ $day['status'] }}
                        </div>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada jadwal</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
