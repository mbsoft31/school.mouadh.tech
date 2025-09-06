<?php

namespace Database\Factories;

use App\Models\LessonProgress;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonProgressFactory extends Factory
{
    protected $model = LessonProgress::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'time_spent_minutes' => $this->faker->numberBetween(5, 60),
            'completed_at' => null,
            // Remove 'is_completed' if it doesn't exist in your table
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => now(),
            'time_spent_minutes' => $this->faker->numberBetween(15, 120),
            // Remove 'is_completed' reference
        ]);
    }
}
