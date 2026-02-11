<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Dokter extends Model
{
    protected $table = 'dokter';
    protected $fillable = [
        'user_id',
        'nip',
        'education',
        'specialization',
        'bio',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduleTemplates()
    {
        return $this->hasMany(DoctorScheduleTemplate::class);
    }

    public function scheduleExceptions()
    {
        return $this->hasMany(DoctorScheduleException::class);
    }
}
