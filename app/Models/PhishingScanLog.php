<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhishingScanLog extends Model
{
    protected $fillable = [
        'user_id',
        'input_type',
        'input_data',
        'risk_level',
        'risk_score',
        'flags',
    ];

    protected $casts = [
        'flags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
