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
        Schema::create('survey_chapter_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SurveyChapterQuestion::class);
            $table->string('answer');
            $table->boolean('has_send_smp')->default(false);
            $table->boolean('has_send_doctor')->default(false);
            $table->boolean('has_attention')->default(false);
            $table->boolean('has_need_consult_doctor')->default(false);
            $table->boolean('has_disable_other_answer')->default(false);
            $table->boolean('has_disable_answers')->default(false);
            $table->boolean('has_show_disp_date_picker')->nullable();
            $table->string('disable_answer_ids')->nullable();
            $table->string('enable_answer_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_chapter_answers');
    }
};
