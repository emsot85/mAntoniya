<?php

namespace App\Models;

use App\Traits\HasUuidPrimaryKeyTrait;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
  
    public $timestamps = false;
    protected $fillable = ['title', 'locale',  'category_id', 
        'auto_translate',];
}
