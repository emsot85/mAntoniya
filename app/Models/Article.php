<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use App\Traits\HasUuidAndSlug;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Article extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    use HasUuid;

    protected $fillable = [
        'slug',
        'uuid',
        'image',
        'category_id',
        'author_id',
        'views_count',
        'likes_count',
        'videos',
        'buttons',
        'status',
        'published_at',
        'is_featured',
    ];

    protected $casts = [
        'videos' => 'array',
        'buttons' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public $translatedAttributes = [
        'meta_title',
        'meta_description',
        'title',
        'description',
        'extra_field_1',
        'extra_field_2',
        'extra_field_3',
        'auto_translate',
    ];

    // 🔹 Связи
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    protected static function booted(): void
    {

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $title = $article->translate('ru', false)?->title
                    ?? $article->translate('en', false)?->title
                    ?? 'article';
                $article->slug = \Illuminate\Support\Str::slug($title);
            }
        });

        static::updating(function ($article) {
            // Если slug пустой или заголовок изменился — обновляем slug
            $title = $article->translate('ru', false)?->title
                ?? $article->translate('en', false)?->title;
            if (!$article->slug || $article->isDirty('translations')) {
                $article->slug = \Illuminate\Support\Str::slug($title ?? 'article');
            }
        });
    }
}
