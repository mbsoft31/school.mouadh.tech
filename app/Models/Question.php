<?php
// app/Models/Question.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'course_id',
        'type',
        'stem',
        'concept_tags',
        'difficulty_level',
        'points',
        'expected_value',
        'tolerance',
        'units',
        'solution_explanation',
    ];

    protected $casts = [
        'concept_tags' => 'array',
        'points' => 'decimal:2',
        'expected_value' => 'decimal:8',
        'tolerance' => 'decimal:8',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class)->orderBy('order_index');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class)
            ->withPivot('order_index')
            ->withTimestamps();
    }

    // Scopes
    public function scopeMultipleChoice($query)
    {
        return $query->where('type', 'multiple_choice');
    }

    public function scopeNumericInput($query)
    {
        return $query->where('type', 'numeric_input');
    }
}
