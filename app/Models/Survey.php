<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'name'
    ];

    public function medCardControlCall()
    {
        return $this->hasMany(MedCardControlCall::class);
    }

    public function surveyChapters()
    {
        return $this->hasMany(SurveyChapter::class);
    }

    public function surveyAnswers()
    {
        return $this->hasManyThrough(SurveyChapterAnswer::class, SurveyChapterQuestion::class, 'survey_id', 'survey_chapter_question_id', 'id', 'id');
    }

    public function surveyChapterQuestions()
    {
        return $this->hasManyThrough(SurveyChapter::class, SurveyChapterQuestion::class);
    }
}
