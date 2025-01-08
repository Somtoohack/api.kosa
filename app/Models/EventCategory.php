<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon']; // Add fillable attributes
}
