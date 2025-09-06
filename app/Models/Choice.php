<?php
// app/Models/Choice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'feedback',
        'order_index',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }
}
