<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorScheduleTemplate extends Model
{
    protected $fillable = [
        'dokter_id',
        'week_cycle', // Opsional jika belum dipakai kompleks, set default 1
        'day',
        'start_time',
        'end_time',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
}