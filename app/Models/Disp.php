<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'start_at',
        'closed_at',
        'deleted_at',
    ];
}
