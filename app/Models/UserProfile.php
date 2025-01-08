<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'device_key',
        'user_tag',
        'device_name',
        'state',
        'city',
        'lga',
        'address',
        'date_of_birth',
    ];

    protected $dates = [
        'date_of_birth',
    ];

    protected $appends = ['full_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        $names = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $names) ?: 'N/A';
    }

}