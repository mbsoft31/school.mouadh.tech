<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['multiple_choice', 'numeric_input']);

        $data = [
            'id' => Str::uuid(),
            'course_id' => Course::factory(),
            'type' => $type,
            'stem' => $this->faker->sentence() . '?',
            'concept_tags' => $this->faker->words(rand(2, 4)),
            'difficulty_level' => $this->faker->numberBetween(1, 5),
            'points' => $this->faker->randomElement([1, 2, 3, 5, 10]),
        ];

        if ($type === 'numeric_input') {
            $data = array_merge($data, [
                'expected_value' => $this->faker->randomFloat(2, 1, 100),
                'tolerance' => $this->faker->randomFloat(2, 0.1, 5),
                'units' => $this->faker->optional()->randomElement(['cm', 'm', 'kg', 'g', 'seconds', '%']),
                'solution_explanation' => $this->faker->sentence(),
            ]);
        }

        return $data;
    }

    /**
     * Indicate that the question is multiple choice.
     */
    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'multiple_choice',
            'expected_value' => null,
            'tolerance' => null,
            'units' => null,
            'solution_explanation' => null,
        ]);
    }

    /**
     * Indicate that the question is numeric input.
     */
    public function numericInput(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'numeric_input',
            'expected_value' => $this->faker->randomFloat(2, 1, 100),
            'tolerance' => $this->faker->randomFloat(2, 0.1, 5),
            'units' => $this->faker->optional()->randomElement(['cm', 'm', 'kg', 'g', 'seconds', '%']),
            'solution_explanation' => $this->faker->sentence(),
        ]);
    }
}
