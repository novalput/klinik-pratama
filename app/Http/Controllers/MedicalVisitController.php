<?php

namespace App\Http\Controllers;

use App\Models\HealthVisit;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\MedicalRecord;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicalVisitController extends Controller
{
    // --- STEP 1: STAFF ADMIN MEMBUAT KUNJUNGAN ---
    public function create(Request $request)
    {
        if (!Auth::user()->canEditAdministrasiData()) {
            abort(403, 'Akses ditolak. Area Staf Administrasi.');
        }

        // 1. SETUP WAKTU SEKARANG (WIB / GMT+7)
        $now = Carbon::now('Asia/Jakarta');
        $currentDate = $now->format('Y-m-d'); // Tanggal: 2026-02-06
        $currentTime = $now->format('H:i:s'); // Jam: 09:30:00
        $currentDayEn = $now->format('l');    // Hari Inggris: Friday

        // Translate Hari ke Indonesia (Sesuai Database)
        $daysMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $daysMap[$currentDayEn];

        // 2. AMBIL SEMUA DOKTER + JADWALNYA
        $allDoctors = Dokter::where('status', 'active') 
            ->with(['user', 'scheduleTemplates', 'scheduleExceptions'])
            ->get();

        // 3. FILTERING: Hanya dokter yang praktik SAAT INI
        $availableDoctors = $allDoctors->filter(function ($dokter) use ($currentDate, $currentTime, $currentDayIndo) {
            
            // A. CEK JADWAL PENGECUALIAN (Prioritas Tertinggi)
            // Cek apakah dokter punya exception di tanggal hari ini?
            $exception = $dokter->scheduleExceptions->firstWhere('date', $currentDate);

            if ($exception) {
                if ($exception->type == 'libur') {
                    return false; // Dokter diliburkan hari ini -> Hide
                } 
                elseif ($exception->type == 'tambahan') {
                    // Cek apakah jam sekarang masuk range jam tambahan
                    return $currentTime >= $exception->start_time && $currentTime <= $exception->end_time;
                }
            }

            // B. CEK JADWAL RUTIN (Jika tidak ada exception)
            // Cek apakah dokter punya template di hari ini?
            $template = $dokter->scheduleTemplates->firstWhere('day', $currentDayIndo);

            if ($template) {
                // Cek apakah jam sekarang masuk range jam praktik rutin
                return $currentTime >= $template->start_time && $currentTime <= $template->end_time;
            }

            return false; // Tidak ada jadwal sama sekali -> Hide
        });

        // 4. Data Pasien (Jika ada parameter patient_id dari URL)
        $patients = Patient::orderBy('name')->get();
        $selectedPatient = null;
        if($request->has('patient_id')) {
            $selectedPatient = Patient::find($request->patient_id);
        }
        
        // Kirim $availableDoctors ke view
        return view('visits.create', [
            'patients' => $patients,
            'doctors' => $availableDoctors, // Hanya dokter yang sedang praktik
            'selectedPatient' => $selectedPatient,
            'currentDayIndo' => $currentDayIndo // Opsional: untuk info di view
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canEditAdministrasiData()) abort(403);

        $now = Carbon::now('Asia/Jakarta');
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        $currentDayEn = $now->format('l');

        $daysMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $daysMap[$currentDayEn];

        // Ambil dokter yang tersedia (SAMA seperti create)
        $availableDoctors = Dokter::where('status', 'active')
            ->with(['scheduleTemplates', 'scheduleExceptions'])
            ->get()
            ->filter(function ($dokter) use ($currentDate, $currentTime, $currentDayIndo) {

                $exception = $dokter->scheduleExceptions->firstWhere('date', $currentDate);
                if ($exception) {
                    if ($exception->type === 'libur') return false;
                    if ($exception->type === 'tambahan') {
                        return $currentTime >= $exception->start_time && $currentTime <= $exception->end_time;
                    }
                }

                $template = $dokter->scheduleTemplates->firstWhere('day', $currentDayIndo);
                if ($template) {
                    return $currentTime >= $template->start_time && $currentTime <= $template->end_time;
                }

                return false;
            });
            
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_date' => 'required|date',
            'dokter_id' => $availableDoctors->isEmpty() 
            ? 'nullable'
            : 'required'.'|exists:dokter,id',
            'visit_type' => 'required',
            'payment_type' => 'required',
            'complaint' => 'nullable|string',
        ]);

        $visit = DB::transaction(function() use ($request) {
            // 1. Staff Admin membuat Visit
            $visit = HealthVisit::create($request->all());

            // 2. Siapkan wadah Medical Record & Profile agar Perawat tinggal Edit
            MedicalRecord::firstOrCreate(['health_visit_id' => $visit->id]);
            PatientProfile::firstOrCreate(['patient_id' => $visit->patient_id]);

            return $visit;
        });

        return redirect()->route('staff_administrasi.monitoring.show', $visit->patient_id)
            ->with('success', 'Kunjungan dibuat. Menunggu pemeriksaan Nakes.');
    }

    // --- STEP 2: FORM EDIT TERPADU (Admin, Nakes, Dokter) ---
    public function edit($id)
    {
        $visit = HealthVisit::with(['patient.patientProfile', 'medicalRecord', 'dokter.user'])->findOrFail($id);
        $user = Auth::user();

        return view('visits.edit', compact('visit', 'user'));
    }

    public function update(Request $request, $id)
    {
        $visit = HealthVisit::findOrFail($id);
        $user = Auth::user();

        DB::transaction(function () use ($request, $visit, $user) {
            
            // === OTORISASI PERAWAT (Isi Profile & Vitals) ===
            if ($user->canEditProfileAndVitals()) {
                
                // Update ke tabel patients_profile
                $visit->patient->patientProfile()->updateOrCreate(
                    ['patient_id' => $visit->patient_id],
                    $request->only(['blood_type', 'allergies', 'bpjs_number', 'occupation', 'education', 'marital_status'])
                );

                // Update ke tabel medical_records (Kolom Vital)
                $visit->medicalRecord()->updateOrCreate(
                    ['health_visit_id' => $visit->id],
                    $request->only(['blood_pressure', 'temperature', 'weight', 'height'])
                );
            }

            // === OTORISASI DOKTER (Isi Diagnosa, Tindakan, Obat) ===
            if ($user->canEditMedicalDiagnosis()) {
                $data = $request->only(['diagnosis_icd10', 'treatment_notes', 'refferal', 'follow_up_plan']);
                
                // Simpan obat sebagai JSON
                if($request->has('medications')) {
                    $data['medications'] = $request->medications; 
                }

                $visit->medicalRecord()->update($data);
                
                // Dokter update Consent di tabel visit
                $visit->update($request->only('consent'));
            }

            // === OTORISASI STAFF ADMIN (Edit Data Kunjungan jika salah input) ===
            if ($user->canEditAdministrasiData()) {
                $visit->update($request->only(['visit_date', 'visit_type', 'payment_type', 'complaint']));
            }
        });

        return back()->with('success', 'Rekam medis berhasil diperbarui.');
    }
}