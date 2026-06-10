<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = [
        'user_id', 'quiz_id',
        'score', 'total', 'percentage',
    ];

    // Add this
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Add this too
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
