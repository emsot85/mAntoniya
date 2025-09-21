<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterTranslation extends Model
{
    protected $fillable = [
         'id',
        'locale',
        'title',
        'content',
        'extra_field_1',
        'extra_field_2',
        'extra_field_3',
        'extra_field_4',
        'auto_translate',
    ];
}
