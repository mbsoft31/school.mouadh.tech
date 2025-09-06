<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence(rand(4, 8)),
            'description' => $this->faker->paragraph(),
            'estimated_duration_minutes' => $this->faker->numberBetween(30, 120),
            'order_index' => $this->faker->numberBetween(1, 10),
            'content_blocks' => $this->generateContentBlocks(),
        ];
    }

    /**
     * Generate sample content blocks
     */
    private function generateContentBlocks(): array
    {
        $blocks = [];
        $blockCount = rand(2, 5);

        for ($i = 0; $i < $blockCount; $i++) {
            $blockType = $this->faker->randomElement(['markdown', 'media']);

            if ($blockType === 'markdown') {
                $blocks[] = [
                    'type' => 'markdown',
                    'content' => $this->faker->paragraphs(rand(2, 4), true),
                ];
            } else {
                $mediaType = $this->faker->randomElement(['image', 'video', 'audio']);
                $blocks[] = [
                    'type' => 'media',
                    'url' => "https://example.com/{$mediaType}s/lesson-{$i}.{$this->getExtension($mediaType)}",
                    'media_type' => $mediaType,
                    'alt_text' => $this->faker->sentence(),
                    'caption' => $this->faker->optional()->sentence(),
                ];
            }
        }

        return $blocks;
    }

    private function getExtension(string $mediaType): string
    {
        return match ($mediaType) {
            'image' => 'jpg',
            'video' => 'mp4',
            'audio' => 'mp3',
        };
    }
}
