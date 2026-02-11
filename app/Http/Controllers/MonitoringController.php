<?php

namespace App\Http\Controllers;

use App\Models\HealthVisit;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Redirect Index ke Data Pasien
     * (Karena monitoring sekarang digabung di menu Data Pasien)
     */
    public function nakesIndex()
    {
        return redirect()->route('nakes.datapasien');
    }

    public function dokterIndex()
    {
        return redirect()->route('dokter.datapasien');
    }

    /**
     * Logic Utama Menampilkan Detail Pasien & Rekam Medis
     * Digunakan oleh: Nakes, Dokter, dan Staff Admin
     */
    private function showPatientDetails($id)
    {
        // 1. Ambil Data Pasien beserta Profil Tambahannya
        $patient = Patient::with('patientProfile')->findOrFail($id);

        // 2. Ambil Riwayat Kunjungan (Health Visits)
        // Kita load 'medicalRecord' agar data Tensi/Diagnosa bisa dibaca di View (JSON)
        // Kita load 'dokter' agar nama dokter muncul
        $visits = HealthVisit::where('patient_id', $id)
            ->with(['dokter.user', 'medicalRecord']) 
            ->orderBy('visit_date', 'desc')
            ->get();

        // 3. Return ke View 'monitoring.show' yang baru (yang ada dropdown)
        return view('monitoring.show', compact('patient', 'visits'));
    }

    /**
     * Method untuk route nakes.monitoring.show / staff_administrasi.monitoring.show
     */
    public function nakesShow($id)
    {
        return $this->showPatientDetails($id);
    }

    /**
     * Method untuk route dokter.monitoring.show
     */
    public function dokterShow($id)
    {
        // Bisa ditambahkan validasi khusus dokter jika perlu
        return $this->showPatientDetails($id);
    }
}