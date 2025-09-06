<?php

namespace Database\Factories;

use App\Models\AssessmentAttempt;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentAttemptFactory extends Factory
{
    protected $model = AssessmentAttempt::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'assessment_id' => Assessment::factory(),
            'score' => null,
            'started_at' => now(),
            'status' => 'in_progress',
            // Remove 'completed_at' and 'is_completed' if they don't exist
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => $this->faker->numberBetween(0, 100),
            'status' => 'completed',
            // Remove 'completed_at' reference if column doesn't exist
        ]);
    }
}
