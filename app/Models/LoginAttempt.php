<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'attempts',
        'is_locked',
        'otp',
        'otp_expires_at',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    public function authenticatable()
    {
        return $this->morphTo();
    }
}