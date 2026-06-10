<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonSection extends Model
{
    protected $fillable = ['lesson_id', 'sort_order', 'type', 'label', 'title', 'body', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
