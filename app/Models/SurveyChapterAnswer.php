<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SurveyChapterAnswer extends Model
{
    protected $fillable = [
        'survey_chapter_question_id',
        'answer',
        'has_send_smp',
        'has_send_doctor',
        'has_attention',
        'has_need_consult_doctor',
        'has_disable_other_answer',
        'has_disable_answers',
        'has_show_disp_date_picker',
        'disable_answer_ids',
        'enable_answer_ids',
    ];

    protected function disableAnswerIds(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value) ? unserialize($value) : null,
        );
    }

    protected function enableAnswerIds(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value) ? unserialize($value) : null,
        );
    }

    public function surveyChapterQuestion()
    {
        return $this->belongsTo(SurveyChapterQuestion::class);
    }
}
