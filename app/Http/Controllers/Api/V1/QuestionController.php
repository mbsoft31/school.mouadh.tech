<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    /**
     * Display questions for a specific course.
     */
    public function index(Request $request, Course $course): AnonymousResourceCollection
    {
        $query = $course->questions();

        // Filter by question type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by difficulty level
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filter by concept tags
        if ($request->filled('concept_tag')) {
            $query->whereJsonContains('concept_tags', $request->concept_tag);
        }

        $questions = $query->with('choices')->paginate($request->get('per_page', 15));

        return QuestionResource::collection($questions);
    }

    /**
     * Display the specified question.
     */
    public function show(Course $course, Question $question): QuestionResource
    {
        // Ensure question belongs to the course
        if ($question->course_id !== $course->id) {
            abort(404);
        }

        $question->load(['choices' => fn($query) => $query->orderBy('order_index')]);

        return new QuestionResource($question);
    }
}
