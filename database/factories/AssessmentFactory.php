<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assessmentTypes = ['formative', 'summative'];
        $titles = [
            'Unit Quiz',
            'Chapter Assessment',
            'Practice Test',
            'Mid-term Evaluation',
            'Final Exam',
            'Skill Check',
            'Review Quiz',
        ];

        return [
            'id' => Str::uuid(),
            'course_id' => Course::factory(),
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement($assessmentTypes),
            'time_limit_minutes' => $this->faker->randomElement([0, 15, 30, 45, 60, 90]),
            'max_attempts' => $this->faker->randomElement([-1, 1, 2, 3, 5]),
            'show_feedback_immediately' => $this->faker->boolean(70),
        ];
    }

    /**
     * Indicate that the assessment is formative.
     */
    public function formative(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'formative',
            'max_attempts' => -1,
            'show_feedback_immediately' => true,
        ]);
    }

    /**
     * Indicate that the assessment is summative.
     */
    public function summative(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'summative',
            'max_attempts' => $this->faker->randomElement([1, 2, 3]),
            'show_feedback_immediately' => false,
        ]);
    }
}
