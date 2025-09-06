<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::query()
            ->withCount(['lessons', 'assessments'])
            ->where('status', '!=', 'archived');

        // Apply filters (same as before)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('subject') && $request->subject !== 'any') {
            $query->where('subject', $request->subject);
        }

        if ($request->filled('grade_level') && $request->grade_level !== 'any') {
            $query->whereJsonContains('grade_levels', $request->grade_level);
        }

        $courses = $query
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($course) => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'subject' => $course->subject,
                'grade_levels' => $course->grade_levels,
                'estimated_duration_minutes' => $course->estimated_duration_minutes,
                'lesson_count' => $course->lessons_count,
                'assessment_count' => $course->assessments_count,
                'created_at' => $course->created_at->toISOString(),
                'updated_at' => $course->updated_at->toISOString(),
            ]);

        return Inertia::render('courses/index', [
            'courses' => $courses, // Laravel will handle the structure automatically
            'filters' => $request->only(['search', 'subject', 'grade_level']),
        ]);
    }

    public function show(Course $course): Response
    {
        $course->load(['lessons', 'assessments', 'questions.choices']);

        return Inertia::render('courses/show', [
            'course' => [
                'id' => $course->id,
                'schema_version' => $course->schema_version,
                'title' => $course->title,
                'description' => $course->description,
                'subject' => $course->subject,
                'grade_levels' => $course->grade_levels,
                'estimated_duration_minutes' => $course->estimated_duration_minutes,
                'standards' => $course->standards,
                'created_at' => $course->created_at->toISOString(),
                'updated_at' => $course->updated_at->toISOString(),
                'author' => $course->author,
                'status' => $course->status,
                'lessons' => $course->lessons->map(fn ($lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'estimated_duration_minutes' => $lesson->estimated_duration_minutes,
                    'order_index' => $lesson->order_index,
                    'content_blocks' => $lesson->content_blocks,
                ]),
                'assessments' => $course->assessments->map(fn ($assessment) => [
                    'id' => $assessment->id,
                    'title' => $assessment->title,
                    'description' => $assessment->description,
                    'type' => $assessment->type,
                    'time_limit_minutes' => $assessment->time_limit_minutes,
                    'max_attempts' => $assessment->max_attempts,
                    'show_feedback_immediately' => $assessment->show_feedback_immediately,
                    'question_ids' => $assessment->questions->pluck('id'),
                ]),
                'question_bank' => $course->questions->keyBy('id')->map(fn ($question) => [
                    'type' => $question->type,
                    'stem' => $question->stem,
                    'concept_tags' => $question->concept_tags,
                    'difficulty_level' => $question->difficulty_level,
                    'points' => $question->points,
                    'choices' => $question->choices?->map(fn ($choice) => [
                        'text' => $choice->text,
                        'is_correct' => $choice->is_correct,
                        'feedback' => $choice->feedback,
                    ]),
                    'expected_value' => $question->expected_value,
                    'tolerance' => $question->tolerance,
                    'units' => $question->units,
                    'solution_explanation' => $question->solution_explanation,
                ]),
            ],
        ]);
    }
}
