<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    //
    protected $fillable = [
        'user_id',
        'wallet_id',
        'reference',
        'type',
    ];

}