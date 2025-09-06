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
        Schema::create('assessment_question', function (Blueprint $table) {
            $table->uuid('assessment_id');
            $table->uuid('question_id');
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->primary(['assessment_id', 'question_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');

            $table->index(['assessment_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_question');
    }
};
