<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory, Translatable;
    use HasUuid;

    protected $fillable = ['slug', 'type', 'is_active', 'uuid'];

    public $translatedAttributes = [
      'page_id',
       'locale',
       'title',
       'description',
       'content',
       'extra_field_1',
       'extra_field_2',
       'extra_field_3',
       'meta_title',
       'meta_description',
      ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
       static::creating(function ($model) {
        if (empty($model->slug)) {
            $title = $model->translate('ru', false)?->title
                  ?? $model->translate('en', false)?->title;

            $baseSlug = $title ? Str::slug($title) : (string) Str::uuid();
            $slug = $baseSlug;
            $i = 1;

            // Проверяем уникальность
            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i;
                $i++;
            }

            $model->slug = $slug;
        }
    });
    }
}
