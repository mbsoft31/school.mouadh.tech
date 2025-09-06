<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'type' => $this->type,
            'stem' => $this->stem,
            'concept_tags' => $this->concept_tags,
            'difficulty_level' => $this->difficulty_level,
            'points' => $this->points,
        ];

        // Add type-specific fields
        if ($this->type === 'multiple_choice') {
            $data['choices'] = $this->when($this->relationLoaded('choices'),
                fn() => $this->choices->map(fn($choice) => [
                    'text' => $choice->text,
                    'is_correct' => $choice->is_correct,
                    'feedback' => $choice->feedback,
                ])->toArray()
            );
        } elseif ($this->type === 'numeric_input') {
            $data['answer'] = [
                'expected_value' => $this->expected_value,
                'tolerance' => $this->tolerance,
                'units' => $this->units,
            ];
            $data['solution_explanation'] = $this->solution_explanation;
        }

        return $data;
    }
}
