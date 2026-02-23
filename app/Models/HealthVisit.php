<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthVisit extends Model
{
    protected $table = 'health_visits';

    protected $fillable = [
        'patient_id',
        'visit_date',
        'dokter_id',
        'complaint',    // Staff Admin isi ini
        'visit_type',   // Staff Admin isi ini
        'payment_type', // Staff Admin isi ini
        'consent',      // Dokter isi ini
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function getStatusLabelAttribute()
    {
        // Pastikan relasi medicalRecord diload
        // Jika belum ada record, berarti baru dibuat Staff -> Menunggu Nakes
        if (!$this->medicalRecord) {
            return 'Menunggu Nakes';
        }

        $mr = $this->medicalRecord;

        // Cek apakah Dokter sudah mengisi Diagnosa?
        if (!empty($mr->diagnosis_icd10)) {
            return 'Selesai';
        }

        // Cek apakah Nakes sudah mengisi Tensi/Vital?
        // (Asumsi: jika tensi terisi, nakes sudah bekerja)
        if (!empty($mr->blood_pressure)) {
            return 'Menunggu Dokter';
        }

        // Jika record ada tapi kosong, berarti masih di Nakes
        return 'Menunggu Nakes';
    }

    // Opsional: Untuk warna badge yang berbeda
    public function getStatusColorAttribute()
    {
        $status = $this->status_label;
        
        return match($status) {
            'Selesai' => 'bg-green-100 text-green-700 border-green-200',
            'Menunggu Dokter' => 'bg-blue-100 text-blue-700 border-blue-200', // Warna Dokter
            'Menunggu Nakes' => 'bg-yellow-100 text-yellow-700 border-yellow-200', // Warna Nakes
            default => 'bg-gray-100 text-gray-700',
        };
    }
}