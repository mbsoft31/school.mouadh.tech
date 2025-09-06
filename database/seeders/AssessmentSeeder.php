<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::with('questions')->get();

        $assessmentTemplates = [
            [
                'title' => 'Unit 1 Quiz',
                'description' => 'Quick formative assessment covering the first unit concepts.',
                'type' => 'formative',
                'time_limit_minutes' => 20,
                'max_attempts' => 3,
                'show_feedback_immediately' => true,
            ],
            [
                'title' => 'Mid-Unit Assessment',
                'description' => 'Comprehensive assessment of key learning objectives.',
                'type' => 'summative',
                'time_limit_minutes' => 45,
                'max_attempts' => 2,
                'show_feedback_immediately' => false,
            ],
            [
                'title' => 'Practice Problems',
                'description' => 'Practice exercises to reinforce learning.',
                'type' => 'formative',
                'time_limit_minutes' => 0, // No time limit
                'max_attempts' => -1, // Unlimited attempts
                'show_feedback_immediately' => true,
            ],
        ];

        foreach ($courses as $course) {
            $courseQuestions = $course->questions;

            if ($courseQuestions->isEmpty()) continue;

            // Create 2-3 assessments per course
            foreach (array_slice($assessmentTemplates, 0, rand(2, 3)) as $index => $template) {
                $assessment = Assessment::create([
                    'id' => Str::uuid(),
                    'course_id' => $course->id,
                    'title' => $template['title'] . " - {$course->title}",
                    'description' => $template['description'],
                    'type' => $template['type'],
                    'time_limit_minutes' => $template['time_limit_minutes'],
                    'max_attempts' => $template['max_attempts'],
                    'show_feedback_immediately' => $template['show_feedback_immediately'],
                    'created_at' => $course->created_at->addDays($index + 1),
                    'updated_at' => $course->updated_at,
                ]);

                // Randomly assign 3-5 questions to each assessment
                $randomQuestions = $courseQuestions->random(min(rand(3, 5), $courseQuestions->count()));

                foreach ($randomQuestions as $index => $question) {
                    $assessment->questions()->attach($question->id, [
                        'order_index' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
