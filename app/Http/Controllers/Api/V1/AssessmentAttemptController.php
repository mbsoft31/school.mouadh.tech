<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AssessmentAttemptController extends Controller
{
    /**
     * Start a new assessment attempt.
     */
    public function start(Request $request, Course $course, Assessment $assessment): JsonResponse
    {
        // Ensure assessment belongs to the course
        if ($assessment->course_id !== $course->id) {
            abort(404);
        }

        $user = $request->user();

        // Check if user has exceeded max attempts
        if ($assessment->max_attempts > 0) {
            $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('user_id', $user->id)
                ->count();

            if ($attemptCount >= $assessment->max_attempts) {
                return response()->json([
                    'message' => 'Maximum attempts exceeded',
                    'error_code' => 'MAX_ATTEMPTS_EXCEEDED'
                ], 400);
            }
        }

        // Load questions for this assessment
        $assessment->load(['questions.choices' => fn($query) => $query->orderBy('order_index')]);

        // Create new attempt
        $attempt = AssessmentAttempt::create([
            'id' => Str::uuid(),
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'started_at' => now(),
            'expires_at' => $assessment->time_limit_minutes > 0
                ? now()->addMinutes($assessment->time_limit_minutes)
                : null,
            'status' => 'in_progress',
        ]);

        // Prepare questions for student (without correct answers)
        $questionsForAttempt = $assessment->questions->map(function ($question) {
            $data = [
                'question_id' => $question->id,
                'type' => $question->type,
                'stem' => $question->stem,
                'points' => $question->points,
            ];

            if ($question->type === 'multiple_choice') {
                $data['choices'] = $question->choices->map(fn($choice) => [
                    'id' => $choice->id,
                    'text' => $choice->text,
                ])->toArray();
            }

            return $data;
        });

        return response()->json([
            'attempt_id' => $attempt->id,
            'assessment_id' => $assessment->id,
            'started_at' => $attempt->started_at->toISOString(),
            'expires_at' => $attempt->expires_at?->toISOString(),
            'questions' => $questionsForAttempt,
        ], 201);
    }

    /**
     * Submit assessment answers.
     */
    public function submit(Request $request, AssessmentAttempt $attempt): JsonResponse
    {
        // Validate that attempt is still active
        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'message' => 'Attempt is no longer active',
                'error_code' => 'ATTEMPT_INACTIVE'
            ], 400);
        }

        // Check if attempt has expired
        if ($attempt->expires_at && $attempt->expires_at->isPast()) {
            $attempt->update([
                'status' => 'expired',
                'submitted_at' => now(),
            ]);

            return response()->json([
                'message' => 'Attempt has expired',
                'error_code' => 'ATTEMPT_EXPIRED'
            ], 400);
        }

        // Validate answers
        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|uuid|exists:questions,id',
            'answers.*.answer' => 'required',
        ]);

        // Load assessment with questions and choices
        $assessment = $attempt->assessment->load(['questions.choices']);

        // Grade the attempt
        $totalScore = 0;
        $maxScore = 0;
        $questionResults = [];

        foreach ($request->answers as $submittedAnswer) {
            $question = $assessment->questions->firstWhere('id', $submittedAnswer['question_id']);

            if (!$question) continue;

            $maxScore += $question->points;
            $isCorrect = false;
            $pointsEarned = 0;
            $feedback = '';

            if ($question->type === 'multiple_choice') {
                $selectedChoice = $question->choices->firstWhere('id', $submittedAnswer['answer']);
                if ($selectedChoice) {
                    $isCorrect = $selectedChoice->is_correct;
                    $pointsEarned = $isCorrect ? $question->points : 0;
                    $feedback = $selectedChoice->feedback;
                }
            } elseif ($question->type === 'numeric_input') {
                $studentAnswer = (float) $submittedAnswer['answer'];
                $expectedValue = $question->expected_value;
                $tolerance = $question->tolerance ?? 0;

                $isCorrect = abs($studentAnswer - $expectedValue) <= $tolerance;
                $pointsEarned = $isCorrect ? $question->points : 0;
                $feedback = $isCorrect ? 'Correct!' : 'Incorrect. The correct answer is ' . $expectedValue;
            }

            $totalScore += $pointsEarned;

            $questionResults[] = [
                'question_id' => $question->id,
                'student_answer' => $submittedAnswer['answer'],
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'points_possible' => $question->points,
                'feedback' => $feedback,
            ];
        }

        // Update attempt
        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $attempt->update([
            'status' => 'completed',
            'submitted_at' => now(),
            'score' => (float)$totalScore,
            'max_score' => (float)$maxScore,
            'percentage' => (float)$percentage,
            'answers' => $request->answers,
        ]);

        $response = [
            'attempt_id' => $attempt->id,
            'submitted_at' => $attempt->submitted_at->toISOString(),
            'score' => (float)$totalScore,
            'max_score' => (float)$maxScore,
            'percentage' => round((float)$percentage, 2),
        ];

        // Include question results if feedback is enabled
        if ($assessment->show_feedback_immediately) {
            $response['question_results'] = $questionResults;
        }

        return response()->json($response);
    }
}
