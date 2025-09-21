<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class MenuController extends Controller
{

  public static function getMenu(string $slug = 'main')
  {
    return Menu::where('slug', $slug)
      ->with([
        'translations',
        'items.translations',
        'items.children.translations',
      ])
      ->first();
  }

  // public static function getMenu(string $name = 'main')
  // {
  //   return Menu::whereHas('translations', function ($query) use ($name) {
  //     $query->where('name', $name)
  //       ->where('locale', app()->getLocale());
  //   })
  //     ->with(['items' => function ($query) {
  //       $query->whereNull('parent_id')
  //         ->with('children.translations', 'translations');
  //     }])
  //     ->first();
  // }

  // public static function getMenu(string $name = 'main')
  // {
  //     $locale = app()->getLocale();

  //     return Menu::whereHas('translations', function ($query) use ($name, $locale) {
  //             $query->where('locale', $locale)
  //                   ->where('name', $name);
  //         })
  //         ->with(['items' => function ($query) use ($locale) {
  //             $query->whereNull('parent_id')
  //                   ->with(['translations', 'children.translations', 'page.translations']);
  //         }])
  //         ->first();
  // }
}
