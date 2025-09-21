<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'name'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
