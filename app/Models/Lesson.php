<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'summary',
        'duration_minutes',
        'points',
        'content',
        'category',
        'difficulty',
        'order_index',
        'is_active',
    ];

    public function progress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(LessonSection::class)->orderBy('sort_order');
    }
}
