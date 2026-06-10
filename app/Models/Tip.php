<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level',
        'priority',
        'is_active',
    ];
}
