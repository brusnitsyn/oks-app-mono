<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'family',
        'name',
        'ot',
        'snils',
        'phone',
        'dop_phone',
        'brith_at',
        'gender_id'
    ];

    public function medcards()
    {
        return $this->hasMany(MedCard::class)->withTrashed();
    }

    public function activeMedcard()
    {
        return $this->hasOne(MedCard::class)
            ->where('closed_at', null)
            ->where('deleted_at', null);
    }

    public function lastMedcard()
    {
        return $this->hasOne(MedCard::class)->withTrashed()->latest();
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
