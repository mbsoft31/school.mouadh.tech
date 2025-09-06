<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'schema_version',
        'title',
        'description',
        'subject',
        'grade_levels',
        'estimated_duration_minutes',
        'standards',
        'author',
        'status',
    ];

    protected $casts = [
        'grade_levels' => 'array',
        'standards' => 'array',
    ];

    // Relationships
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // Computed attributes
    public function getLessonCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    public function getAssessmentCountAttribute(): int
    {
        return $this->assessments()->count();
    }
}
