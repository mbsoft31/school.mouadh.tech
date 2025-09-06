<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'schema_version' => '2.0.0',
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(3, true),
            'subject' => $this->faker->randomElement([
                'Mathematics',
                'Science',
                'English',
                'Social Studies',
                'History'
            ]),
            'grade_levels' => $this->faker->randomElement([
                ['9'],
                ['10'],
                ['11'],
                ['12'],
                ['9', '10'],
                ['10', '11'],
                ['11', '12']
            ]),
            'estimated_duration_minutes' => $this->faker->numberBetween(300, 3000),
            'standards' => [
                'CCSS.MATH.CONTENT.HSA.REI.A.1',
                'CCSS.MATH.CONTENT.HSA.REI.B.3',
                'NGSS.HS-PS1-2'
            ],
            'author' => $this->faker->name(),
            // Fix: Use only valid status values
            'status' => $this->faker->randomElement(['draft', 'active', 'archived']),
        ];
    }

    // Add state methods for specific statuses
    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function archived(): static
    {
        return $this->state(['status' => 'archived']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }
}
