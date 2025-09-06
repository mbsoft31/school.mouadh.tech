<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 courses with full relationships
        Course::factory(15)
            ->published()
            ->create()
            ->each(function (Course $course) {
                // Create 4-8 lessons per course
                Lesson::factory(rand(4, 8))
                    ->sequence(fn($sequence) => ['order_index' => $sequence->index + 1])
                    ->create(['course_id' => $course->id]);

                // Create 8-15 questions per course
                $questions = Question::factory(rand(8, 15))
                    ->create(['course_id' => $course->id]);

                // Create choices for multiple choice questions
                $questions->filter(fn($q) => $q->type === 'multiple_choice')
                    ->each(function (Question $question) {
                        $choiceCount = rand(3, 4);
                        $correctIndex = rand(0, $choiceCount - 1);

                        for ($i = 0; $i < $choiceCount; $i++) {
                            Choice::factory()
                                ->state([
                                    'question_id' => $question->id,
                                    'order_index' => $i + 1,
                                    'is_correct' => $i === $correctIndex,
                                    'text' => $i === $correctIndex
                                        ? 'This is the correct answer'
                                        : "Distractor option " . ($i + 1),
                                    'feedback' => $i === $correctIndex
                                        ? 'Correct! Well done.'
                                        : 'Incorrect. Try again.',
                                ])
                                ->create();
                        }
                    });

                // Create 2-4 assessments per course
                Assessment::factory(rand(2, 4))
                    ->create(['course_id' => $course->id])
                    ->each(function (Assessment $assessment) use ($questions) {
                        // Attach 3-6 random questions to each assessment
                        $randomQuestions = $questions->random(rand(3, min(6, $questions->count())));

                        // FIX: Cast UUID objects to strings for array keys
                        $attachData = $randomQuestions->mapWithKeys(function ($question, $index) {
                            return [(string) $question->id => ['order_index' => $index + 1]];
                        });

                        $assessment->questions()->attach($attachData);
                    });
            });

        $this->command->info('Created courses with lessons, assessments, and questions.');
    }
}
