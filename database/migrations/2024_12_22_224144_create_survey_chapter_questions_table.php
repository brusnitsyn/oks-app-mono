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
        Schema::create('survey_chapter_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Survey::class);
            $table->foreignIdFor(\App\Models\SurveyChapter::class);
            $table->string('question');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_chapter_questions');
    }
};
