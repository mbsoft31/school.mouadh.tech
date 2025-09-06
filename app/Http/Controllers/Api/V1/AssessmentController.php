<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssessmentResource;
use App\Models\Course;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AssessmentController extends Controller
{
    /**
     * Display assessments for a specific course.
     */
    public function index(Request $request, Course $course): AnonymousResourceCollection
    {
        $query = $course->assessments();

        // Filter by assessment type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $assessments = $query->paginate($request->get('per_page', 15));

        return AssessmentResource::collection($assessments);
    }

    /**
     * Display the specified assessment.
     */
    public function show(Course $course, Assessment $assessment): AssessmentResource
    {
        // Ensure assessment belongs to the course
        if ($assessment->course_id !== $course->id) {
            abort(404);
        }

        $assessment->load(['questions']);

        return new AssessmentResource($assessment);
    }
}
