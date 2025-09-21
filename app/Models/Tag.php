<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasUuidPrimaryKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Tag extends Model implements TranslatableContract
{
    use Translatable;
    use HasUuid;
    
    public $timestamps = false;
    
    protected $fillable = ['slug', 'uuid'];

    public $translatedAttributes = ['title'];
}
