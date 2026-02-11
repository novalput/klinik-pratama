<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    protected $table = 'patients_profile';

    protected $fillable = [
        'patient_id',
        'blood_type',
        'allergies',
        'bpjs_number',
        'occupation',
        'education',
        'marital_status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}   