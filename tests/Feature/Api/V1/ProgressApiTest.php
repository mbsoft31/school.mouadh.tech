<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\User;
use App\Models\LessonProgress;
use App\Models\AssessmentAttempt;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->published()->create();
});

describe('Progress Tracking API', function () {

    it('requires authentication to view progress', function () {
        $response = $this->getJson("/api/v1/courses/{$this->course->id}/progress");

        $response->assertUnauthorized();
    });

    it('returns comprehensive course progress for authenticated user', function () {
        $lessons = Lesson::factory(3)->create(['course_id' => $this->course->id]);
        $assessments = Assessment::factory(2)->create(['course_id' => $this->course->id]);

        // Create some progress
        LessonProgress::factory()->completed()->create([
            'user_id' => $this->user->id,
            'lesson_id' => $lessons->first()->id,
            'time_spent_minutes' => 45,
        ]);

        AssessmentAttempt::factory()->completed()->create([
            'assessment_id' => $assessments->first()->id,
            'user_id' => $this->user->id,
            'percentage' => 85.5,
        ]);

        actingAsUser($this->user);

        $response = $this->getJson("/api/v1/courses/{$this->course->id}/progress");

        $response->assertOk()
            ->assertJsonStructure([
                'course_id',
                'user_id',
                'overall_progress_percentage',
                'lessons_completed',
                'total_lessons',
                'assessments_completed',
                'total_assessments',
                'average_assessment_score',
                'time_spent_minutes',
                'lesson_progress' => [
                    '*' => [
                        'lesson_id',
                        'completed',
                        'completed_at',
                        'time_spent_minutes',
                    ]
                ],
                'assessment_progress' => [
                    '*' => [
                        'assessment_id',
                        'attempts',
                        'best_score',
                        'latest_attempt_at',
                    ]
                ],
            ]);

        expect($response->json('course_id'))->toBe((string) $this->course->id)
            ->and($response->json('user_id'))->toBe((string) $this->user->id)
            ->and($response->json('lessons_completed'))->toBe(1)
            ->and($response->json('total_lessons'))->toBe(3)
            ->and($response->json('assessments_completed'))->toBe(1)
            ->and($response->json('total_assessments'))->toBe(2)
            ->and($response->json('time_spent_minutes'))->toBe(45)
            ->and($response->json('average_assessment_score'))->toBe(85.5);
    });

    it('calculates progress percentage correctly', function () {
        // Create specific test data with known values
        $lesson1 = Lesson::factory()->create(['course_id' => $this->course->id]);
        $lesson2 = Lesson::factory()->create(['course_id' => $this->course->id]);

        // Complete exactly one lesson (50% of 2 lessons)
        LessonProgress::factory()->completed()->create([
            'user_id' => $this->user->id,
            'lesson_id' => $lesson1->id,
        ]);

        $response = $this->getJson("/api/v1/courses/{$this->course->id}/progress");

        $response->assertOk();

        expect($response->json('overall_progress_percentage'))->toBe(50.0);
    });

    it('requires authentication to complete lesson', function () {
        $lesson = Lesson::factory()->create(['course_id' => $this->course->id]);

        $response = $this->postJson("/api/v1/lessons/{$lesson->id}/complete", [
            'time_spent_minutes' => 30
        ]);

        $response->assertUnauthorized();
    });

    it('allows authenticated user to complete lesson', function () {
        $lesson = Lesson::factory()->create(['course_id' => $this->course->id]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/lessons/{$lesson->id}/complete", [
            'time_spent_minutes' => 45
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'lesson_id',
                'completed_at',
                'time_spent_minutes',
            ]);

        expect($response->json('lesson_id'))->toBe((string) $lesson->id)
            ->and($response->json('time_spent_minutes'))->toBe(45);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->user->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
            'time_spent_minutes' => 45,
        ]);
    });

    it('validates lesson completion data', function () {
        $lesson = Lesson::factory()->create(['course_id' => $this->course->id]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/lessons/{$lesson->id}/complete", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['time_spent_minutes']);
    });

    it('updates existing lesson progress', function () {
        $lesson = Lesson::factory()->create(['course_id' => $this->course->id]);

        $existingProgress = LessonProgress::factory()->create([
            'user_id' => $this->user->id,
            'lesson_id' => $lesson->id,
            'completed' => false,
            'time_spent_minutes' => 20,
        ]);

        actingAsUser($this->user);

        $response = $this->postJson("/api/v1/lessons/{$lesson->id}/complete", [
            'time_spent_minutes' => 50
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('lesson_progress', [
            'id' => $existingProgress->id,
            'user_id' => $this->user->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
            'time_spent_minutes' => 50,
        ]);

        // Ensure no duplicate records
        expect(App\Models\LessonProgress::where('user_id', $this->user->id)
            ->where('lesson_id', $lesson->id)
            ->count())->toBe(1);
    });
});
