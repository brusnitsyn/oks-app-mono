<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedCardControlCall extends Model
{
    protected $fillable = [
        'med_card_id',
        'name',
        'call_at',
        'called_at',
        'control_call_result_id',
        'survey_result_id',
        'survey_id',
        'info'
    ];

    public function med_card()
    {
        return $this->belongsTo(MedCard::class, 'med_card_id');
    }

    public function med_control_call_result()
    {

    }

    public function surveyAnswers()
    {
        return $this->hasMany(MedCardSurveyAnswer::class, 'med_card_control_call_id', 'id');
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
}
