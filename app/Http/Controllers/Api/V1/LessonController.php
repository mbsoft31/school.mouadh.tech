<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends Controller
{
    /**
     * Display lessons for a specific course.
     */
    public function index(Request $request, Course $course): AnonymousResourceCollection
    {
        $lessons = $course->lessons()
            ->orderBy('order_index')
            ->paginate($request->get('per_page', 15));

        return LessonResource::collection($lessons);
    }

    /**
     * Display the specified lesson.
     */
    public function show(Course $course, Lesson $lesson): LessonResource
    {
        // Ensure lesson belongs to the course
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        return new LessonResource($lesson);
    }
}
