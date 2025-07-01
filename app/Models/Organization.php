<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'organization_id');
    }
}
