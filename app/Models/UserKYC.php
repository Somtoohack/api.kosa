<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserKYC extends Model
{
    protected $table = 'user_kycs';

    protected $fillable = [
        'user_id',
        'bvn',
        'nin',
        'nin_validated',
        'bvn_validated',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}