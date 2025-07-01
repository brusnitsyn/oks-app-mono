<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public function getSlugSourceColumn(): string
    {
        return 'name';
    }

    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $source = $model->getSlugSourceColumn();
            $model->slug = Str::slug($model->$source);
        });
    }
}
