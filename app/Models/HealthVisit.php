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
}