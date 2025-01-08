<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pin',
    ];

    protected $hidden = [
        'pin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}