<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'completed_at',
        'time_spent_minutes',
        'id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'completed' => 'boolean',
        'time_spent_minutes' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
