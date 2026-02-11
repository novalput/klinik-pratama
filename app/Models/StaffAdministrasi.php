<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAdministrasi extends Model
{
    //
    protected $table = 'staff_administrasi';

    protected $fillable = [
        'user_id',
        'nip',
        'address',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
