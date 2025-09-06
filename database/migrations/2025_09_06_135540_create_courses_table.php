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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('schema_version')->default('2.0.0');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject');
            $table->jsonb('grade_levels'); // Store as JSON array
            $table->integer('estimated_duration_minutes')->nullable();
            $table->jsonb('standards')->nullable(); // Curriculum standards
            $table->string('author')->nullable();
            $table->enum('status', ['draft', 'active', 'archived', 'inactive'])->default('draft');
            $table->timestamps();

            $table->index(['subject', 'status']);
        });

        // Add GIN indexes separately with proper operator class
        if (config('database.default') === 'pgsql') {
            DB::statement('CREATE INDEX courses_grade_levels_gin_index ON courses USING gin (grade_levels jsonb_path_ops)');
            DB::statement('CREATE INDEX courses_standards_gin_index ON courses USING gin (standards jsonb_path_ops)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
