<?php

namespace App\Models;

use App\Traits\HasUuidAndSlug;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Category extends Model implements TranslatableContract
{
    use Translatable;
    use HasUuidAndSlug;

    public $timestamps = false;

    protected $fillable = ['slug', 'uuid'];

    public $translatedAttributes = ['title'];

    public function getTitleForFilterAttribute()
    {
        return $this->title; // вернёт текущий locale
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

}