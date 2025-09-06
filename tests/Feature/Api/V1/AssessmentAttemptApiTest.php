<?php

use App\Models\Course;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Choice;
use App\Models\User;
use App\Models\AssessmentAttempt;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->published()->create();
    $this->assessment = Assessment::factory()->create(['course_id' => $this->course->id]);

    // Create questions with choices
    $this->multipleChoiceQuestion = Question::factory()->multipleChoice()->create(['course_id' => $this->course->id]);
    $this->correctChoice = Choice::factory()->correct()->create(['question_id' => $this->multipleChoiceQuestion->id]);
    Choice::factory(3)->incorrect()->create(['question_id' => $this->multipleChoiceQuestion->id]);

    $this->numericQuestion = Question::factory()->numericInput()->create(['course_id' => $this->course->id]);

    $this->assessment->questions()->attach([
        (string)$this->multipleChoiceQuestion->id => ['order_index' => 1],
        (string)$this->numericQuestion->id => ['order_index' => 2],
    ]);
});

describe('Assessment Attempts API', function () {

    it('requires authentication to start assessment', function () {
        $response = $this->postJson("/api/v1/courses/{$this->course->id}/assessments/{$this->assessment->id}/start");

        $response->assertUnauthorized();
    });

    it('allows authenticated user to start assessment', function () {
        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/courses/{$this->course->id}/assessments/{$this->assessment->id}/start");

        $response->assertCreated()
            ->assertJsonStructure([
                'attempt_id',
                'assessment_id',
                'started_at',
                'expires_at',
                'questions' => [
                    '*' => [
                        'question_id',
                        'type',
                        'stem',
                        'points',
                    ]
                ]
            ]);

        expect($response->json('assessment_id'))->toBe((string)$this->assessment->id)
            ->and($response->json('attempt_id'))->toBeUuid();

        $this->assertDatabaseHas('assessment_attempts', [
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
            'status' => 'in_progress',
        ]);
    });

    it('returns questions without correct answers for security', function () {
        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/courses/{$this->course->id}/assessments/{$this->assessment->id}/start");

        $response->assertCreated();

        $questions = $response->json('questions');
        $multipleChoiceQuestion = collect($questions)->firstWhere('type', 'multiple_choice');

        expect($multipleChoiceQuestion)->toHaveKey('choices');

        foreach ($multipleChoiceQuestion['choices'] as $choice) {
            expect($choice)->not->toHaveKey('is_correct')
                ->and($choice)->not->toHaveKey('feedback')
                ->and($choice)->toHaveKey('id')
                ->and($choice)->toHaveKey('text');
        }
    });

    it('prevents starting when max attempts exceeded', function () {
        $this->assessment->update(['max_attempts' => 1]);

        AssessmentAttempt::factory()->completed()->create([
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/courses/{$this->course->id}/assessments/{$this->assessment->id}/start");

        $response->assertBadRequest()
            ->assertJson([
                'message' => 'Maximum attempts exceeded',
                'error_code' => 'MAX_ATTEMPTS_EXCEEDED'
            ]);
    });

    it('can submit assessment answers successfully', function () {
        $attempt = AssessmentAttempt::factory()->create([
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
            'status' => 'in_progress',
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/submit", [
            'answers' => [
                [
                    'question_id' => $this->multipleChoiceQuestion->id,
                    'answer' => $this->correctChoice->id,
                ],
                [
                    'question_id' => $this->numericQuestion->id,
                    'answer' => $this->numericQuestion->expected_value,
                ]
            ]
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'attempt_id',
                'submitted_at',
                'score',
                'max_score',
                'percentage',
            ]);

        expect($response->json('attempt_id'))->toBe($attempt->id)
            ->and($response->json('score'))->toBeGreaterThan(0)
            ->and($response->json('percentage'))->toBeGreaterThan(0);

        $this->assertDatabaseHas('assessment_attempts', [
            'id' => $attempt->id,
            'status' => 'completed',
        ]);
    });

    it('validates submission data', function () {
        $attempt = AssessmentAttempt::factory()->create([
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
            'status' => 'in_progress',
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/submit", [
            'answers' => []
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['answers']);
    });

    it('prevents submission of inactive attempts', function () {
        $attempt = AssessmentAttempt::factory()->completed()->create([
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/submit", [
            'answers' => [
                [
                    'question_id' => $this->multipleChoiceQuestion->id,
                    'answer' => $this->correctChoice->id,
                ]
            ]
        ]);

        $response->assertBadRequest()
            ->assertJson([
                'message' => 'Attempt is no longer active',
                'error_code' => 'ATTEMPT_INACTIVE'
            ]);
    });

    it('calculates scores correctly', function () {
        $attempt = AssessmentAttempt::factory()->create([
            'assessment_id' => $this->assessment->id,
            'user_id' => $this->user->id,
            'status' => 'in_progress',
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/submit", [
            'answers' => [
                [
                    'question_id' => $this->multipleChoiceQuestion->id,
                    'answer' => $this->correctChoice->id, // Correct answer
                ],
                [
                    'question_id' => $this->numericQuestion->id,
                    'answer' => 999, // Incorrect answer
                ]
            ]
        ]);

        $response->assertOk();

        $totalPoints = $this->multipleChoiceQuestion->points + $this->numericQuestion->points;
        $earnedPoints = $this->multipleChoiceQuestion->points; // Only first answer correct
        $expectedPercentage = ($earnedPoints / $totalPoints) * 100;

        expect($response->json('score'))->toBe((float) $earnedPoints)
            ->and($response->json('max_score'))->toBe((float) $totalPoints)
            ->and($response->json('percentage'))->toBe(round($expectedPercentage, 2));
    });
});
