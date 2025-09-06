<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assessment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'time_limit_minutes',
        'max_attempts',
        'show_feedback_immediately',
    ];

    protected $casts = [
        'show_feedback_immediately' => 'boolean',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'assessment_question')
            ->withPivot('order_index')
            ->withTimestamps()
            ->orderBy('assessment_question.order_index');
    }
}
