<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        $questionTemplates = [
            'Mathematics' => [
                [
                    'type' => 'multiple_choice',
                    'stem' => 'What is the value of x in the equation 2x + 5 = 13?',
                    'concept_tags' => ['linear-equations', 'solving', 'algebra'],
                    'difficulty_level' => 2,
                    'points' => 5,
                    'choices' => [
                        ['text' => 'x = 3', 'is_correct' => false, 'feedback' => 'Incorrect. Remember to subtract 5 from both sides first.'],
                        ['text' => 'x = 4', 'is_correct' => true, 'feedback' => 'Correct! 2(4) + 5 = 8 + 5 = 13'],
                        ['text' => 'x = 6', 'is_correct' => false, 'feedback' => 'Incorrect. Check your arithmetic when isolating x.'],
                        ['text' => 'x = 9', 'is_correct' => false, 'feedback' => 'Incorrect. This would give us 2(9) + 5 = 23, not 13.'],
                    ],
                ],
                [
                    'type' => 'numeric_input',
                    'stem' => 'If y = 3x - 7 and x = 5, what is the value of y?',
                    'concept_tags' => ['substitution', 'linear-equations', 'evaluation'],
                    'difficulty_level' => 1,
                    'points' => 3,
                    'expected_value' => 8,
                    'tolerance' => 0,
                    'units' => '',
                    'solution_explanation' => 'Substitute x = 5 into the equation: y = 3(5) - 7 = 15 - 7 = 8',
                ],
                [
                    'type' => 'multiple_choice',
                    'stem' => 'What is the slope of the line passing through points (2, 4) and (6, 12)?',
                    'concept_tags' => ['slope', 'coordinate-geometry', 'graphing'],
                    'difficulty_level' => 3,
                    'points' => 4,
                    'choices' => [
                        ['text' => '1', 'is_correct' => false, 'feedback' => 'Incorrect. Use the slope formula: (y₂ - y₁)/(x₂ - x₁)'],
                        ['text' => '2', 'is_correct' => true, 'feedback' => 'Correct! Slope = (12-4)/(6-2) = 8/4 = 2'],
                        ['text' => '4', 'is_correct' => false, 'feedback' => 'Incorrect. This is the change in y, not the slope.'],
                        ['text' => '8', 'is_correct' => false, 'feedback' => 'Incorrect. Remember to divide rise by run.'],
                    ],
                ],
            ],
            'Science' => [
                [
                    'type' => 'multiple_choice',
                    'stem' => 'How many protons does a carbon atom have?',
                    'concept_tags' => ['atomic-structure', 'periodic-table', 'protons'],
                    'difficulty_level' => 1,
                    'points' => 2,
                    'choices' => [
                        ['text' => '4', 'is_correct' => false, 'feedback' => 'Incorrect. This is not the atomic number of carbon.'],
                        ['text' => '6', 'is_correct' => true, 'feedback' => 'Correct! Carbon has atomic number 6, meaning 6 protons.'],
                        ['text' => '8', 'is_correct' => false, 'feedback' => 'Incorrect. This is the atomic number of oxygen.'],
                        ['text' => '12', 'is_correct' => false, 'feedback' => 'Incorrect. This is the mass number, not the proton count.'],
                    ],
                ],
                [
                    'type' => 'numeric_input',
                    'stem' => 'What is the molar mass of water (H₂O) in g/mol? (H = 1, O = 16)',
                    'concept_tags' => ['molar-mass', 'molecular-weight', 'chemistry-calculations'],
                    'difficulty_level' => 2,
                    'points' => 5,
                    'expected_value' => 18,
                    'tolerance' => 0.1,
                    'units' => 'g/mol',
                    'solution_explanation' => 'H₂O has 2 hydrogen atoms (2 × 1 = 2) and 1 oxygen atom (1 × 16 = 16). Total: 2 + 16 = 18 g/mol',
                ],
                [
                    'type' => 'multiple_choice',
                    'stem' => 'Which type of bond forms between sodium (Na) and chlorine (Cl) in table salt?',
                    'concept_tags' => ['chemical-bonding', 'ionic-bonds', 'compounds'],
                    'difficulty_level' => 2,
                    'points' => 3,
                    'choices' => [
                        ['text' => 'Covalent bond', 'is_correct' => false, 'feedback' => 'Incorrect. Covalent bonds involve sharing electrons.'],
                        ['text' => 'Ionic bond', 'is_correct' => true, 'feedback' => 'Correct! Na loses an electron to Cl, forming an ionic bond.'],
                        ['text' => 'Metallic bond', 'is_correct' => false, 'feedback' => 'Incorrect. Metallic bonds occur between metal atoms.'],
                        ['text' => 'Hydrogen bond', 'is_correct' => false, 'feedback' => 'Incorrect. Hydrogen bonds are intermolecular forces.'],
                    ],
                ],
            ],
        ];

        foreach ($courses as $course) {
            $subject = $course->subject;
            $templates = $questionTemplates[$subject] ?? $questionTemplates['Science'];

            // Create 6-10 questions per course
            for ($i = 0; $i < rand(6, 10); $i++) {
                $template = $templates[array_rand($templates)];

                $questionData = [
                    'id' => Str::uuid(),
                    'course_id' => $course->id,
                    'type' => $template['type'],
                    'stem' => $template['stem'] . " (Course: {$course->title})",
                    'concept_tags' => $template['concept_tags'],
                    'difficulty_level' => $template['difficulty_level'],
                    'points' => $template['points'],
                    'created_at' => $course->created_at->addHours(rand(1, 48)),
                    'updated_at' => $course->updated_at,
                ];

                if ($template['type'] === 'numeric_input') {
                    $questionData = array_merge($questionData, [
                        'expected_value' => $template['expected_value'],
                        'tolerance' => $template['tolerance'],
                        'units' => $template['units'],
                        'solution_explanation' => $template['solution_explanation'],
                    ]);
                }

                $question = Question::create($questionData);

                // Create choices for multiple choice questions
                if ($template['type'] === 'multiple_choice') {
                    foreach ($template['choices'] as $index => $choice) {
                        $question->choices()->create([
                            'id' => Str::uuid(),
                            'text' => $choice['text'],
                            'is_correct' => $choice['is_correct'],
                            'feedback' => $choice['feedback'],
                            'order_index' => $index + 1,
                        ]);
                    }
                }
            }
        }
    }
}
