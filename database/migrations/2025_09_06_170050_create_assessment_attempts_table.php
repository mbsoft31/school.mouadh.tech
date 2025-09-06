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
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id');
            $table->uuid('user_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->jsonb('answers')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'assessment_id']);
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_attempts');
    }
};
