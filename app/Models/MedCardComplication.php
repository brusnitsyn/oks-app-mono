<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedCardComplication extends Model
{
    protected $fillable = [
        'med_card_id',
        'complication_id',
    ];

    public function medcard()
    {
        return $this->belongsTo(Medcard::class);
    }

    public function complication()
    {
        return $this->belongsTo(Complication::class);
    }
}
