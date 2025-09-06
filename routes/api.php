<?php

use App\Http\Controllers\Api\V1\AssessmentAttemptController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\AssessmentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// V1 API Routes
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public Course Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Nested Resource Routes
    Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])->name('courses.lessons.index');
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('courses.lessons.show');

    Route::get('/courses/{course}/assessments', [AssessmentController::class, 'index'])->name('courses.assessments.index');
    Route::get('/courses/{course}/assessments/{assessment}', [AssessmentController::class, 'show'])->name('courses.assessments.show');

    Route::get('/courses/{course}/questions', [QuestionController::class, 'index'])->name('courses.questions.index');
    Route::get('/courses/{course}/questions/{question}', [QuestionController::class, 'show'])->name('courses.questions.show');

    // Protected routes (require authentication)
    Route::middleware(['auth:sanctum'])->group(function () {

        // Assessment interaction endpoints
        Route::post('/courses/{course}/assessments/{assessment}/start',
            [AssessmentAttemptController::class, 'start'])->name('assessments.start');
        Route::post('/attempts/{attempt}/submit',
            [AssessmentAttemptController::class, 'submit'])->name('attempts.submit');

        // Progress tracking endpoints
        Route::get('/courses/{course}/progress',
            [ProgressController::class, 'show'])->name('progress.show');
        Route::post('/lessons/{lesson}/complete',
            [ProgressController::class, 'completeLesson'])->name('lessons.complete');
    });
});


// User authentication route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
