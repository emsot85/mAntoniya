<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'title',
        'auto_translate', 'url'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
