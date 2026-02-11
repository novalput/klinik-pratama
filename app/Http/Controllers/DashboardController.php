<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Workers;
use App\Models\HealthVisit;

class DashboardController extends Controller
{
    /**
     * Private function: Hanya untuk mengambil data (DRY - Don't Repeat Yourself)
     * Logika hitungan statistik ditaruh di sini semua.
     */
    private function getSummaryData()
    {
        return [
            'totalPatients'    => Patient::count(),
            'totalVisits' => HealthVisit::count(),
            'totalNakes'       => User::where('role', 'nakes')->count(),
            'last7DaysVisits'      => HealthVisit::where('visit_date', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Method untuk DOKTER
     * URL: /dokter/dashboard
     */
    public function dokter()
    {
        $data = $this->getSummaryData();
        // Jika dokter butuh data tambahan khusus, tambahkan di sini
        // $data['jadwalHariIni'] = ...; 
        
        return view('dokter.dashboard', $data);
    }

    /**
     * Method untuk NAKES
     * URL: /nakes/dashboard
     */
    public function nakes()
    {
        $data = $this->getSummaryData();
        return view('nakes.dashboard', $data);
    }

    /**
     * Method untuk STAFF ADMINISTRASI
     * URL: /staff_administrasi/dashboard
     */
    public function staff()
    {
        $data = $this->getSummaryData();
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