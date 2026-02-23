<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan Auth
use App\Models\Patient;
use App\Models\User;
use App\Models\Workers;
use App\Models\HealthVisit;

class DashboardController extends Controller
{
    private function getSummaryData()
    {
        return [
            'totalPatients'   => Patient::count(),
            'totalVisits'     => HealthVisit::count(),
            'totalNakes'      => User::where('role', 'nakes')->count(),
            'last7DaysVisits' => HealthVisit::where('visit_date', '>=', now()->subDays(7))->count(),
        ];
    }

    // === DASHBOARD DOKTER ===
    public function dokter()
    {
        $data = $this->getSummaryData();
        $user = Auth::user();

        // Ambil ID dokter dari user yang login
        // Pastikan relasi 'dokter' ada di model User
        $dokterId = $user->dokter ? $user->dokter->id : null;

        if ($dokterId) {
            // Tampilkan pasien yang terjadwal UNTUK DOKTER INI HARI INI
            // Diurutkan dari yang paling pagi/awal dibuat
            $data['tableTitle'] = 'Jadwal Pasien Anda Hari Ini';
            $data['visits'] = HealthVisit::with('patient')
                ->where('dokter_id', $user->dokter->id)
                ->whereDate('visit_date', now()) // Hanya hari ini
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $data['tableTitle'] = 'Data Kunjungan';
            $data['visits'] = [];
        }
        
        return view('dokter.dashboard', $data);
    }

    // === DASHBOARD NAKES ===
    public function nakes()
    {
        $data = $this->getSummaryData();

        // Nakes perlu melihat SEMUA pasien hari ini (untuk dipersiapkan Tanda Vitalnya)
        // Tidak peduli dokternya siapa
        $data['tableTitle'] = 'Daftar Antrean Pasien Hari Ini (Perlu Tanda Vital)';
        $data['visits'] = HealthVisit::with(['patient', 'dokter.user'])
            ->whereDate('visit_date', now())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('nakes.dashboard', $data);
    }

    // === DASHBOARD STAFF ADMIN ===
    public function staff()
    {
        $data = $this->getSummaryData();

        // Admin butuh log aktivitas terbaru (misal: 10 kunjungan terakhir yang diinput)
        // Agar bisa cek apakah data baru masuk atau tidak
        $data['tableTitle'] = 'Riwayat Kunjungan Terbaru (Live Feed)';
        $data['visits'] = HealthVisit::with(['patient', 'dokter.user'])
            ->latest() // Paling baru di atas
            ->limit(10)
            ->get();

        return view('staff_administrasi.dashboard', $data);
    }
    
    private function getSummaryDataAdmin()
    {
        return [
            'totalDoctors'    => User::where('role', 'dokter')->count(),
            'totalStaff'      => User::where('role', 'staf_administrasi')->count(),
            'totalWorkers'   => Workers::count(),        
        ];
    }
    /**
     * Method untuk ADMIN
     * URL: /admin/dashboard
     */
    public function admin()
    {
        $data = $this->getSummaryData();
        $data2 = $this->getSummaryDataAdmin();

        return view('admin.dashboard', array_merge($data, $data2));
    }
}