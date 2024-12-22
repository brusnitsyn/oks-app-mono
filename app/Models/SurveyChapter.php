<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyChapter extends Model
{
    protected $fillable = [
        'survey_id',
        'name'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function questions()
    {
        return $this->hasMany(SurveyChapterQuestion::class);
    }
}
