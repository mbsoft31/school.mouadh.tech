<?php

use App\Models\Course;
use App\Models\Lesson;

it('can list lessons for a course', function () {
    $course = Course::factory()->published()->create();
    $lessons = Lesson::factory(3)->create(['course_id' => $course->id]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/lessons");

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(3)
        ->and($response->json('data.0'))->toHaveKeys(['id', 'title', 'description', 'order_index']);
});

it('orders lessons by order_index', function () {
    $course = Course::factory()->published()->create();

    $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order_index' => 2]);
    $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order_index' => 1]);
    $lesson3 = Lesson::factory()->create(['course_id' => $course->id, 'order_index' => 3]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/lessons");

    $response->assertOk();

    expect($response->json('data.0.id'))->toBe((string)$lesson2->id) // order_index 1
    ->and($response->json('data.1.id'))->toBe((string)$lesson1->id) // order_index 2
    ->and($response->json('data.2.id'))->toBe((string)$lesson3->id); // order_index 3
});

it('can show individual lesson', function () {
    $course = Course::factory()->published()->create();
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/lessons/{$lesson->id}");

    $response->assertOk();

    expect($response->json('data.id'))->toBe((string)$lesson->id)
        ->and($response->json('data.title'))->toBe($lesson->title);
});

it('returns 404 for lesson not belonging to course', function () {
    $course1 = Course::factory()->create();
    $course2 = Course::factory()->create();
    $lesson = Lesson::factory()->create(['course_id' => $course2->id]);

    $response = $this->getJson("/api/v1/courses/{$course1->id}/lessons/{$lesson->id}");

    $response->assertNotFound();
});
