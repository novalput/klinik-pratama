<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Workers extends Model
{
    protected $table = 'workers';
    protected $fillable = [
        'name',
        'nip',
        'position',
        'phone',
        'address',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
}
