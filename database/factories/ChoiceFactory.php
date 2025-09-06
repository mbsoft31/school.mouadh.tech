<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Choice>
 */
class ChoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $choices = [
            'A common incorrect answer',
            'The correct answer option',
            'Another plausible distractor',
            'A less likely but possible choice',
        ];

        return [
            'id' => Str::uuid(),
            'text' => $this->faker->randomElement($choices),
            'is_correct' => false, // Default to false, will be overridden in seeder
            'feedback' => $this->faker->sentence(8),
            'order_index' => 1,
        ];
    }

    /**
     * Mark this choice as correct.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
            'feedback' => 'Correct! ' . $this->faker->sentence(6),
        ]);
    }

    /**
     * Mark this choice as incorrect.
     */
    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
            'feedback' => 'Incorrect. ' . $this->faker->sentence(8),
        ]);
    }
}
