<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mkb extends Model
{
    protected $fillable = [
        'ds',
        'name',
        'has_attendant'
    ];
}
