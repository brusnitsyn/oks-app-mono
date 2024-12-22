<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMedCard extends Model
{
    protected $fillable = [
        'patient_id',
        'medcard_id',
    ];

    public function medcard()
    {
        return $this->belongsTo(Medcard::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
