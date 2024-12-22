<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedCardSurveyAnswer extends Model
{
    protected $fillable = [
        'med_card_id',
        'med_card_control_call_id',
        'survey_id',
        'survey_chapter_question_id',
        'survey_chapter_answer_id',
    ];

    public function medCard()
    {
        return $this->belongsTo(MedCard::class, 'med_card_id');
    }

    public function medCardControlCall()
    {
        return $this->belongsTo(MedCardControlCall::class, 'med_card_control_call_id');
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function surveyChapterQuestion()
    {
        return $this->belongsTo(SurveyChapterQuestion::class, 'survey_chapter_question_id');
    }

    public function surveyChapterAnswer()
    {
        return $this->belongsTo(SurveyChapterAnswer::class, 'survey_chapter_answer_id');
    }
}
