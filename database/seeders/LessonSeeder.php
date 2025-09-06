<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        $lessonTemplates = [
            'Mathematics' => [
                [
                    'title' => 'What are Variables?',
                    'description' => 'Introduction to variables and their role in algebra',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Understanding Variables\n\nA variable is a symbol (usually a letter) that represents a number whose value we do not yet know.\n\n## Examples\n- x, y, z are common variables\n- Variables can represent any number\n- We use variables to create equations"],
                        ['type' => 'media', 'url' => 'https://example.com/videos/variables-intro.mp4', 'media_type' => 'video', 'alt_text' => 'Introduction to Variables Video', 'caption' => 'Watch this video to understand variables better'],
                    ],
                ],
                [
                    'title' => 'Solving Linear Equations',
                    'description' => 'Step-by-step approach to solving linear equations',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Solving Linear Equations\n\nTo solve a linear equation, we need to isolate the variable on one side.\n\n## Steps:\n1. Simplify both sides\n2. Move variables to one side\n3. Move constants to the other side\n4. Divide to isolate the variable"],
                    ],
                ],
                [
                    'title' => 'Graphing Linear Equations',
                    'description' => 'Learn to graph linear equations on coordinate planes',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Graphing Linear Equations\n\nLinear equations create straight lines when graphed.\n\n## Key Concepts:\n- Slope: rise over run\n- Y-intercept: where the line crosses the y-axis\n- X-intercept: where the line crosses the x-axis"],
                        ['type' => 'media', 'url' => 'https://example.com/images/linear-graph.png', 'media_type' => 'image', 'alt_text' => 'Example of Linear Graph', 'caption' => 'Example of a linear equation graphed'],
                    ],
                ],
            ],
            'Science' => [
                [
                    'title' => 'Atomic Structure',
                    'description' => 'Understanding protons, neutrons, and electrons',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Atomic Structure\n\nAtoms are the basic building blocks of matter.\n\n## Components:\n- **Protons**: Positive charge, in nucleus\n- **Neutrons**: No charge, in nucleus\n- **Electrons**: Negative charge, orbit nucleus"],
                        ['type' => 'media', 'url' => 'https://example.com/images/atom-diagram.png', 'media_type' => 'image', 'alt_text' => 'Atom Diagram', 'caption' => 'Basic structure of an atom'],
                    ],
                ],
                [
                    'title' => 'Chemical Bonding',
                    'description' => 'How atoms connect to form compounds',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Chemical Bonding\n\nAtoms bond together to form molecules and compounds.\n\n## Types of Bonds:\n- **Ionic**: Transfer of electrons\n- **Covalent**: Sharing of electrons\n- **Metallic**: Sea of electrons"],
                    ],
                ],
                [
                    'title' => 'The Periodic Table',
                    'description' => 'Organization and patterns in the periodic table',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# The Periodic Table\n\nThe periodic table organizes elements by their properties.\n\n## Organization:\n- Periods: Horizontal rows\n- Groups/Families: Vertical columns\n- Elements with similar properties are grouped together"],
                    ],
                ],
            ],
            'English Language Arts' => [
                [
                    'title' => 'Character Development',
                    'description' => 'Creating compelling characters in your stories',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Character Development\n\nStrong characters are the heart of any good story.\n\n## Key Elements:\n- Motivation: What drives your character?\n- Flaws: What makes them human?\n- Growth: How do they change?\n- Voice: How do they speak and think?"],
                    ],
                ],
                [
                    'title' => 'Plot Structure',
                    'description' => 'Understanding the elements of plot',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Plot Structure\n\nMost stories follow a similar structure.\n\n## Elements:\n1. **Exposition**: Setting and characters\n2. **Rising Action**: Building tension\n3. **Climax**: The turning point\n4. **Falling Action**: Consequences\n5. **Resolution**: Conclusion"],
                    ],
                ],
                [
                    'title' => 'Dialogue Writing',
                    'description' => 'Writing realistic and engaging dialogue',
                    'content_blocks' => [
                        ['type' => 'markdown', 'content' => "# Writing Dialogue\n\nGood dialogue advances the plot and reveals character.\n\n## Tips:\n- Each character should have a unique voice\n- Use subtext - what's not said is often important\n- Keep it concise and purposeful\n- Read it aloud to check flow"],
                    ],
                ],
            ],
        ];

        foreach ($courses as $course) {
            $subject = $course->subject;
            $templates = $lessonTemplates[$subject] ?? $lessonTemplates['Science'];

            foreach ($templates as $index => $template) {
                Lesson::create([
                    'id' => Str::uuid(),
                    'course_id' => $course->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'estimated_duration_minutes' => rand(30, 90),
                    'order_index' => $index + 1,
                    'content_blocks' => $template['content_blocks'],
                    'created_at' => $course->created_at->addDays($index),
                    'updated_at' => $course->updated_at,
                ]);
            }
        }
    }
}
