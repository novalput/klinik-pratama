<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\HealthVisit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function medicalRecord(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_ids'  => 'required|array|min:1', // Harus pilih minimal 1
            'visit_ids.*' => 'exists:health_visits,id',
        ]);

        // 2. Ambil Data Pasien & Profile
        $patient = Patient::with('patientProfile')->findOrFail($request->patient_id);

        // 3. Ambil Kunjungan yang DIPILIH saja (beserta Medical Record & Dokter)
        // Kita urutkan dari yang terlama ke terbaru (kronologis) untuk laporan
        $visits = HealthVisit::with(['medicalRecord', 'dokter.user'])
            ->whereIn('id', $request->visit_ids)
            ->where('patient_id', $patient->id)
            ->orderBy('visit_date', 'asc') 
            ->get();

        // 4. Setup Data untuk View PDF
        $data = [
            'patient' => $patient,
            'visits' => $visits,
            'clinic_name' => "Klinik Pratama Ramah", // Ganti sesuai nama klinik
            'clinic_address' => 'Jl. Santai Sejenak No. 11, Kel. Rehat, Kec. Tenang, Kabupaten Damai, Provinsi Tentram, 77122',
            'date_printed' => Carbon::now()->translatedFormat('d F Y'),
        ];

        // 5. Generate PDF
        // 'a4', 'portrait' bisa diganti 'landscape' jika tabelnya lebar
        $pdf = Pdf::loadView('exports.medical_record_pdf', $data)
                  ->setPaper('a4', 'portrait');

        // Nama file saat didownload
        $filename = 'Rekam_Medis_' . str_replace(' ', '_', $patient->name) . '.pdf';

        return $pdf->stream($filename); // 'stream' untuk preview di browser, 'download' untuk langsung unduh
    }
}