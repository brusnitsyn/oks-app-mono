<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedCardMkbAccompanying extends Model
{
    protected $fillable = [
        'med_card_id',
        'mkb_id'
    ];

    public function medcard()
    {
        return $this->belongsTo(Medcard::class);
    }

    public function mkb()
    {
        return $this->belongsTo(Mkb::class);
    }
}
