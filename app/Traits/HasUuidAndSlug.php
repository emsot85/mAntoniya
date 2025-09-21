<?php

namespace App\Traits;

trait HasUuidAndSlug
{
  protected static function bootHasUuidAndSlug(): void
  {
      static::creating(function ($model) {
          // UUID
          if (property_exists($model, 'uuid') && empty($model->uuid)) {
              $model->uuid = (string) \Illuminate\Support\Str::uuid();
          }

          // slug
          if (property_exists($model, 'slug') && empty($model->slug)) {
              $title = null;

              // Только если есть метод translate
              if (method_exists($model, 'translate')) {
                  $title = $model->translate('ru', false)?->title
                      ?? $model->translate('en', false)?->title;
              }

              $model->slug = $title
                  ? \Illuminate\Support\Str::slug($title)
                  : (string) \Illuminate\Support\Str::uuid();
          }
      });
  }
}
