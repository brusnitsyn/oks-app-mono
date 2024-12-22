<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyChapterQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'survey_chapter_id',
        'question'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function chapter()
    {
        return $this->belongsTo(SurveyChapter::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyChapterAnswer::class);
    }
}
