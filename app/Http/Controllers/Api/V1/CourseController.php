<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseSummaryResource;
use App\Http\Resources\CoursePackageResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Course::query()
            ->withCount(['lessons', 'assessments'])
            ->where('status', '!=', 'archived');

        // Apply filters
        if ($request->filled('grade_level')) {
            $query->whereJsonContains('grade_levels', $request->grade_level);
        }

        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Apply search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $perPage = min($request->get('per_page', 20), 100); // Max 100 per page
        $courses = $query->latest('updated_at')->paginate($perPage);

        return CourseSummaryResource::collection($courses);
    }

    /**
     * Display the specified course.
     *
     * @param Course $course
     * @return CoursePackageResource
     */
    public function show(Course $course): CoursePackageResource
    {
        $course->load([
            'lessons' => fn($query) => $query->orderBy('order_index'),
            'assessments',
            'questions.choices' => fn($query) => $query->orderBy('order_index'),
        ]);

        return new CoursePackageResource($course);
    }
}
