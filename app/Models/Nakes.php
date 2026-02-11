<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Nakes extends Model
{
    protected $table = 'nakes';
    protected $fillable = [
        'user_id',
        'nip',
        'education',
        'specialization',
        'position',
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

}
