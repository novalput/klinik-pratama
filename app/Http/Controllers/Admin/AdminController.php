<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workers;
use App\Models\Patient;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
        $patients = $query->with('patientProfile')->latest()->paginate(15)->withQueryString();

        return view('admin.patient.index', compact('patients'));
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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($user->role !== 'patient') {
            abort(403, 'Unauthorized');
        }

        // Load profil pasien
        $user->load('patient.patientProfile');

        // Ambil riwayat kunjungan untuk dropdown monitoring
        $visits = \App\Models\HealthVisit::where('patient_id', $user->patient->id)
            ->with(['dokter.user', 'medicalRecord']) 
            ->orderBy('visit_date', 'desc')
            ->get();

        // Kita kirim ke view khusus admin atau gunakan view monitoring dengan flag read-only
        return view('admin.patient.show', [
            'patient' => $user->patient,
            'visits' => $visits,
            'user' => $user
        ]);
    }

    public function workers()
    {
        $worker = Workers::latest()
            ->paginate(10);

        return view('admin.workers.index', compact('worker'));
    }

    public function storeWorkers(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|unique:workers,nip',
            'position' => 'nullable|string',
            'phone' => 'nullable|string|max:20|unique:workers,phone',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($data) {

            $worker = Workers::create([
                'name' => $data['name'],
                'nip' => $data['nip'],
                'position' => $data['position'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'] ?? 'active',
                ]);

        });

        return response()->json([
            'message' => 'Karyawan berhasil ditambahkan'
        ], 201);
    }

    public function updateWorkers(Request $request, $id)
    {
        $worker = Workers::findOrFail($id);

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'nip' => [
                    'required',
                    Rule::unique('workers', 'nip')->ignore($worker->id)
                ],
                'position' => 'nullable|string|max:255',
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('workers', 'phone')->ignore($worker->id)
                ],
                'address' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            DB::transaction(function () use ($worker, $data) {
                $worker->update([
                    'name' => $data['name'],
                    'nip' => $data['nip'],
                    'position' => $data['position'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'status' => $data['status'],
                ]);
            });

            return response()->json([
                'message' => 'Data karyawan berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Gagal update karyawan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function destroyWorkers($id)
    {
        $worker = Workers::findOrFail($id);
        $worker->delete(); 

        return response()->json([
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }
}
