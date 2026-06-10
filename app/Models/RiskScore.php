<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'level',
        'quiz_weight',
        'behavior_weight',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
