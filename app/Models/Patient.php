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
    ];

    public function medcards()
    {
        return $this->hasMany(Medcard::class)->withTrashed();
    }

    public function activeMedcard()
    {
        return $this->hasOne(Medcard::class)
            ->where('closed_at', null)
            ->where('deleted_at', null);
    }

    public function lastMedcard()
    {
        return $this->hasOne(Medcard::class)->withTrashed()->latest();
    }
}
