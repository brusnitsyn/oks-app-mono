<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Patient extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Patient $patient) {
            $patient->full_name = "$patient->family $patient->name $patient->ot";
        });
    }

    protected $fillable = [
        'family',
        'name',
        'ot',
        'snils',
        'phone',
        'dop_phone',
        'brith_at',
        'gender_id',
        'full_name',

        'fias_objectid',
        'address'
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

    public function toSearchableArray(): array
    {
        return array_merge([
            'id' => (string) $this->id,
            'full_name' => (string) $this->full_name,
            'snils' => (string) $this->snils,
            'phone' => (string) $this->phone,
            'created_at' => $this->created_at->timestamp,
        ]);
    }

    // Индекс для поискового движка
    public function searchableAs(): string
    {
        return 'oks_patients_index';
    }
}
