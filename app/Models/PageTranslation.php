<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
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
      'auto_translate',
      ];
}
