<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Menampilkan daftar pasien
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Logic Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('nik', 'LIKE', "%{$search}%")
                ->orWhere('no_rm', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
        }

        // Load relasi profile
        $patients = $query->with('patientProfile')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('nakes.datapasien', compact('patients'));
    }

    /**
     * Simpan data pasien baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Hapus field pregnancy)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|unique:patients,nik|max:16',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 2. Generate No RM Otomatis (Format: YM-XXXX, contoh: 2602-0001)
                $prefix = date('ym'); 
                $lastPatient = Patient::where('no_rm', 'like', "$prefix-%")->orderBy('id', 'desc')->first();
                $lastNo = $lastPatient ? intval(substr($lastPatient->no_rm, -4)) : 0;
                $newNoRM = $prefix . '-' . str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);

                // 3. Simpan Data Pasien Utama
                $patient = Patient::create([
                    'no_rm' => $newNoRM,
                    'nik' => $validated['nik'],
                    'name' => $validated['name'],
                    'gender' => $validated['gender'],
                    'dob' => $validated['dob'],
                    'address' => $validated['address'],
                    'phone' => $validated['phone'],
                ]);

                // 4. Buat Profile Pasien Kosong (Agar siap diedit Nakes nanti)
                PatientProfile::create([
                    'patient_id' => $patient->id,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pasien berhasil didaftarkan.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update data pasien
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => "required|string|max:20",
            'nik' => "required|string|unique:patients,nik,{$patient->id}",
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $patient) {
                // Update Tabel Patients
                $patient->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'nik' => $validated['nik'],
                    'dob' => $validated['dob'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'],
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Data pasien berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Pasien
     */
    public function destroy($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete(); // Cascade delete akan menghapus profile & visits

            return response()->json(['success' => true, 'message' => 'Data pasien berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}