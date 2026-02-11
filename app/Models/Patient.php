<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'no_rm',
        'nik',
        'name',
        'gender',
        'dob',
        'address',
        'phone',
    ];

    // Relasi ke Profil Tambahan (Gol darah, BPJS, Pekerjaan)
    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class);
    }

    // Relasi ke Riwayat Kunjungan
    public function healthVisits()
    {
        return $this->hasMany(HealthVisit::class);
    }
}