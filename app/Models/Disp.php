<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disp extends Model
{
    protected $fillable = [
        'start_at',
        'closed_at',
        'mkb_id',
        'deleted_at',
    ];

    public function mkb()
    {
        return $this->belongsTo(Mkb::class);
    }
}
