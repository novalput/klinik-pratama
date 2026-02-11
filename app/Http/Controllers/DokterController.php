<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\HealthVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DokterController extends Controller
{
    public function schedule(Request $request)
    {
        $user = Auth::user();
        $dokter = $user->dokter;

        // 1. Tentukan Bulan & Tahun yang dilihat (Default: Bulan ini)
        $date = $request->has('month') 
            ? Carbon::createFromFormat('Y-m', $request->month)->startOfMonth() 
            : Carbon::now()->startOfMonth();

        $prevMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');
        
        // 2. Siapkan data Calendar
        $daysInMonth = $date->daysInMonth;
        $startDayOfWeek = $date->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
        
        // Ambil semua exception di bulan ini sekaligus (agar tidak query di dalam loop)
        $exceptions = $dokter->scheduleExceptions()
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        // Ambil template rutin
        $templates = $dokter->scheduleTemplates->keyBy('day');

        $calendar = [];

        // Helper mapping hari Inggris ke Indo (sesuai database kamu)
        $dayMap = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        // 3. Loop tanggal 1 sampai akhir bulan
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $date->copy()->day($i);
            $dateString = $currentDate->format('Y-m-d');
            $dayNameIndo = $dayMap[$currentDate->dayOfWeekIso];

            // LOGIC PENENTUAN JADWAL
            $status = null;
            $time = '-';
            $type = 'empty'; // empty, routine, exception-off, exception-on

            // Cek 1: Apakah ada Exception (Prioritas Tertinggi)
            if ($exceptions->has($dateString)) {
                $exc = $exceptions[$dateString];
                if ($exc->type == 'libur') {
                    $type = 'exception-off';
                    $status = 'LIBUR / CUTI';
                } else {
                    $type = 'exception-on';
                    $status = 'Tambahan';
                    $time = substr($exc->start_time, 0, 5) . ' - ' . substr($exc->end_time, 0, 5);
                }
            } 
            // Cek 2: Jika tidak ada exception, cek Template Rutin
            elseif ($templates->has($dayNameIndo)) {
                $temp = $templates[$dayNameIndo];
                $type = 'routine';
                $status = 'Praktik';
                $time = substr($temp->start_time, 0, 5) . ' - ' . substr($temp->end_time, 0, 5);
            }

            $calendar[] = [
                'date' => $i,
                'full_date' => $dateString,
                'day_iso' => $currentDate->dayOfWeekIso,
                'type' => $type,
                'status' => $status,
                'time' => $time,
                'is_today' => $dateString === Carbon::now()->format('Y-m-d')
            ];
        }

        return view('dokter.schedule', compact('calendar', 'date', 'startDayOfWeek', 'prevMonth', 'nextMonth'));
    }

    // Helper sederhana untuk translate hari (bisa dipindah ke helper global)
    private function translateDay($englishDay) {
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        return $days[$englishDay] ?? $englishDay;
    }
}
