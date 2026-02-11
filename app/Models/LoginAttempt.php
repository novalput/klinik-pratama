<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'destination',
        'channel',
        'ip_address',
        'success'
    ];

    protected $casts = [
        'success' => 'boolean',
    ];
}
