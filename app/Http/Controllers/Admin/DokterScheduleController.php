<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\DoctorScheduleTemplate;
use App\Models\DoctorScheduleException;
use Illuminate\Http\Request;

class DokterScheduleController extends Controller
{
    // Halaman manajemen jadwal untuk 1 dokter spesifik
    public function manage($dokterId)
    {
        $dokter = Dokter::with(['user', 'scheduleTemplates', 'scheduleExceptions' => function($q) {
            $q->whereDate('date', '>=', now())->orderBy('date');
        }])->findOrFail($dokterId);

        return view('admin.dokter.schedule', compact('dokter'));
    }

    // Simpan Template (Jadwal Rutin)
    public function storeTemplate(Request $request, $dokterId)
    {
        $request->validate([
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        DoctorScheduleTemplate::create([
            'dokter_id' => $dokterId,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'week_cycle' => 1, // Default 1
        ]);

        return back()->with('success', 'Jadwal rutin berhasil ditambahkan');
    }

    // Hapus Template
    public function destroyTemplate($id)
    {
        DoctorScheduleTemplate::destroy($id);
        return back()->with('success', 'Jadwal rutin dihapus');
    }

    // Simpan Exception (Libur/Tambahan)
    public function storeException(Request $request, $dokterId)
    {
        // Debugging: Pastikan Dokter ID Valid
        $dokter = Dokter::findOrFail($dokterId);

        try {
            $request->validate([
                'date' => 'required|date', // Hapus 'after_or_equal:today' sementara untuk testing
                'type' => 'required|in:libur,tambahan',
                // Validasi jam hanya jika tipe 'tambahan'
                'start_time' => 'nullable|required_if:type,tambahan', 
                'end_time'   => 'nullable|required_if:type,tambahan|after:start_time',
            ]);

            DoctorScheduleException::create([
                'dokter_id' => $dokterId,
                'date' => $request->date,
                'type' => $request->type,
                // Pastikan null jika tipe libur
                'start_time' => $request->type == 'tambahan' ? $request->start_time : null,
                'end_time'   => $request->type == 'tambahan' ? $request->end_time : null,
            ]);

            return back()->with('success', 'Pengecualian jadwal berhasil disimpan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi agar kita tahu salahnya dimana
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Hapus Exception
    public function destroyException($id)
    {
        DoctorScheduleException::destroy($id);
        return back()->with('success', 'Pengecualian dihapus');
    }
}