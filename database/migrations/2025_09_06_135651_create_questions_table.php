<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('course_id');
            $table->enum('type', ['multiple_choice', 'numeric_input']);
            $table->text('stem'); // Question text
            $table->jsonb('concept_tags');
            $table->integer('difficulty_level')->default(3); // 1-5 scale
            $table->decimal('points', 8, 2)->default(5.00);

            // For numeric input questions
            $table->decimal('expected_value', 15, 8)->nullable();
            $table->decimal('tolerance', 15, 8)->nullable();
            $table->string('units')->nullable();
            $table->text('solution_explanation')->nullable();

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index(['course_id', 'type']);
        });

        // Add GIN index for concept_tags if using PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::statement('CREATE INDEX questions_concept_tags_gin_index ON questions USING gin (concept_tags jsonb_path_ops)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
