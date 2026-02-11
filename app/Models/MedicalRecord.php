<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';

    protected $fillable = [
        'health_visit_id',
        // --- Diisi Perawat ---
        'blood_pressure',
        'temperature',
        'weight',
        'height',
        // --- Diisi Dokter ---
        'diagnosis_icd10',
        'treatment_notes',
        'medications', // Simpan sebagai JSON
        'refferal',
        'follow_up_plan',
    ];

    protected $casts = [
        'medications' => 'array',
    ];

    public function healthVisit()
    {
        return $this->belongsTo(HealthVisit::class);
    }
}