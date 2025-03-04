<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{

    protected $fillable = [
        'email',
        'loggable_id',
        'loggable_type',
        'device_id',
        'device_name',
        'ip_address',
        'user_agent',
        'login_at',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }
}