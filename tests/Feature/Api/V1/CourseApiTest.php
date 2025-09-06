<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;

beforeEach(function () {
    $this->seed();
});

describe('Course API', function () {

    it('can list courses', function () {
        Course::factory(3)->published()->create();

        $response = $this->getJson('/api/v1/courses');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'subject',
                        'grade_levels',
                        'estimated_duration_minutes',
                        'lesson_count',
                        'assessment_count',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        expect($response->json('data'))->toBeArray()
            ->and(count($response->json('data')))->toBeGreaterThan(0)
            ->and($response->json('meta'))->toHavePaginationStructure();
    });

    it('can filter courses by subject', function () {
        // Create exactly the data this test needs
        $mathCourse = Course::factory()->create([
            'subject' => 'Mathematics',
            'status' => 'active'
        ]);

        $scienceCourse = Course::factory()->create([
            'subject' => 'Science',
            'status' => 'active'
        ]);

        $response = $this->getJson('/api/v1/courses?subject=Mathematics');

        $response->assertOk();

        expect($response->json('meta.total'))->toBe(1)
            ->and($response->json('data.0.subject'))->toBe('Mathematics');
    });

    it('can filter courses by grade level', function () {
        // Create specific test data
        $grade9Course = Course::factory()->create([
            'grade_levels' => ['9'],
            'status' => 'active'
        ]);

        $grade10Course = Course::factory()->create([
            'grade_levels' => ['10'],
            'status' => 'active'
        ]);

        $response = $this->getJson('/api/v1/courses?grade_level=9');

        $response->assertOk();

        expect($response->json('meta.total'))->toBe(1)
            ->and($response->json('data.0.grade_levels'))->toContain('9');
    });

    it('can search courses by title', function () {
        // Create specific test data
        $mathCourse = Course::factory()->create([
            'title' => 'Advanced Mathematics',
            'status' => 'active'
        ]);

        $englishCourse = Course::factory()->create([
            'title' => 'English Literature',
            'status' => 'active'
        ]);

        $response = $this->getJson('/api/v1/courses?search=mathematics');

        $response->assertOk();

        expect($response->json('meta.total'))->toBe(1)
            ->and($response->json('data.0.title'))->toContain('Mathematics');
    });

    it('can show single course with relationships', function () {
        $course = Course::factory()->published()
            ->has(Lesson::factory(2))
            ->has(Assessment::factory(1))
            ->has(Question::factory(3))
            ->create();

        $response = $this->getJson("/api/v1/courses/{$course->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'schema_version',
                    'title',
                    'lessons',
                    'assessments',
                    'question_bank',
                ]
            ]);

        expect($response->json('data.id'))->toBe((string)$course->id)
            ->and($response->json('data.lessons'))->toHaveCount(2)
            ->and($response->json('data.assessments'))->toHaveCount(1)
            ->and($response->json('data.question_bank'))->toHaveCount(3);
    });

    it('returns 404 for non-existent course', function () {
        $response = $this->getJson('/api/v1/courses/non-existent-id');

        $response->assertNotFound();
    });

    it('paginates courses correctly', function () {
        // Create exactly 25 active courses for this test
        Course::factory()->count(25)->create(['status' => 'active']);

        $response = $this->getJson('/api/v1/courses?per_page=10');

        $response->assertOk();

        expect(count($response->json('data')))->toBe(10)
            ->and($response->json('meta.total'))->toBe(25)
            ->and($response->json('meta.last_page'))->toBe(3);
    });

    it('limits maximum per page to 100', function () {
        Course::factory(150)->published()->create();

        $response = $this->getJson('/api/v1/courses?per_page=200');

        $response->assertOk();

        expect(count($response->json('data')))->toBeLessThanOrEqual(100);
    });

    it('excludes archived courses from listing', function () {
        // Create specific test data
        $activeCourse = Course::factory()->create(['status' => 'active']);
        $archivedCourse = Course::factory()->create(['status' => 'archived']);

        $response = $this->getJson('/api/v1/courses');

        $response->assertOk();

        expect($response->json('meta.total'))->toBe(1)
            ->and($response->json('data.0.status'))->not->toBe('archived');
    });
});
