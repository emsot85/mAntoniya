<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meta_title',
        'meta_description',
        'locale',
        'title',
        'description',
        'extra_field_1',
        'extra_field_2',
        'extra_field_3',
        'auto_translate',
    ];
}
