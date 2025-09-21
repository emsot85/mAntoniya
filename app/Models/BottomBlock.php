<?php 

// app/Models/BottomBlock.php
namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class BottomBlock extends Model implements TranslatableContract
{

    use HasFactory, Translatable;

    protected $fillable = ['id',];

     public $translatedAttributes = [
        'locale',
        'title',
        'content',
        'extra_field_1',
        'extra_field_2',
        'extra_field_3',
        'extra_field_4',
        'image',
        'videos',
        'buttons',
        'auto_translate',
      ];

    protected $casts = [
        'videos' => 'array',
        'buttons' => 'array',
    ];
   
}
