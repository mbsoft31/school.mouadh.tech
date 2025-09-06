<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProgressController extends Controller
{
    /**
     * Get course progress for authenticated user.
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        // Get lesson progress
        $lessonProgress = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson', fn($query) => $query->where('course_id', $course->id))
            ->get()
            ->keyBy('lesson_id');

        // Get assessment attempts
        $assessmentProgress = $user->assessmentAttempts()
            ->whereHas('assessment', fn($query) => $query->where('course_id', $course->id))
            ->selectRaw('assessment_id, COUNT(*) as attempts, MAX(percentage) as best_score, MAX(submitted_at) as latest_attempt_at')
            ->groupBy('assessment_id')
            ->get()
            ->keyBy('assessment_id');

        // Calculate overall progress
        $totalLessons = $course->lessons()->count();
        $completedLessons = $lessonProgress->where('completed', true)->count();
        $totalAssessments = $course->assessments()->count();
        $completedAssessments = $assessmentProgress->count();

        $overallProgress = $totalLessons > 0
            ? ($completedLessons / $totalLessons) * 100
            : 0;

        $averageScore = $assessmentProgress->where('best_score', '>', 0)->avg('best_score');
        $totalTimeSpent = $lessonProgress->sum('time_spent_minutes');

        return response()->json([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'overall_progress_percentage' => round($overallProgress, 2),
            'lessons_completed' => $completedLessons,
            'total_lessons' => $totalLessons,
            'assessments_completed' => $completedAssessments,
            'total_assessments' => $totalAssessments,
            'average_assessment_score' => $averageScore ? round($averageScore, 2) : null,
            'time_spent_minutes' => $totalTimeSpent,
            'lesson_progress' => $course->lessons->map(fn($lesson) => [
                'lesson_id' => $lesson->id,
                'completed' => $lessonProgress->has($lesson->id) && $lessonProgress[$lesson->id]->completed,
                'completed_at' => $lessonProgress->has($lesson->id)
                    ? $lessonProgress[$lesson->id]->completed_at?->toISOString()
                    : null,
                'time_spent_minutes' => $lessonProgress->get($lesson->id)?->time_spent_minutes ?? 0,
            ]),
            'assessment_progress' => $course->assessments->map(fn($assessment) => [
                'assessment_id' => $assessment->id,
                'attempts' => $assessmentProgress->get($assessment->id)?->attempts ?? 0,
                'best_score' => $assessmentProgress->get($assessment->id)?->best_score,
                'latest_attempt_at' => $assessmentProgress->get($assessment->id)?->latest_attempt_at?->toISOString(),
            ]),
        ]);
    }

    /**
     * Mark lesson as complete.
     */
    public function completeLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'time_spent_minutes' => 'required|numeric|min:0',
        ]);

        $user = $request->user();

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'id' => Str::uuid(),
                'completed' => true,
                'completed_at' => now(),
                'time_spent_minutes' => $request->time_spent_minutes,
            ]
        );

        return response()->json([
            'message' => 'Lesson completed successfully',
            'lesson_id' => $lesson->id,
            'completed_at' => $progress->completed_at->toISOString(),
            'time_spent_minutes' => $progress->time_spent_minutes,
        ]);
    }
}
