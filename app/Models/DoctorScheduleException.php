<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorScheduleException extends Model
{
    protected $fillable = [
        'dokter_id',
        'date',
        'start_time',
        'end_time',
        'type', // libur, tambahan
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
}