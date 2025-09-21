<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuidPrimaryKeyTrait
{
    
    protected static function bootHasUuidPrimaryKeyTrait()
    {
        static::creating(function ($model) {
            // Генерируем UUID, если его нет
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
