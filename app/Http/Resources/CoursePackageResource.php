<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursePackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schema_version' => $this->schema_version,
            'title' => $this->title,
            'description' => $this->description,
            'subject' => $this->subject,
            'grade_levels' => $this->grade_levels,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'standards' => $this->standards,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'author' => $this->author,
            'status' => $this->status,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'assessments' => AssessmentResource::collection($this->whenLoaded('assessments')),
            'question_bank' => $this->when($this->relationLoaded('questions'),
                fn() => $this->questions->keyBy('id')->map(fn($question) => new QuestionResource($question))
            ),
        ];
    }
}
