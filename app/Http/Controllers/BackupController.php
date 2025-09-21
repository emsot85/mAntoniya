<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Menu;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;

class BackupController extends Controller
{
  public function export()
  {
    $data = [
      'categories' => Category::with('translations')->get(),
      'articles' => Article::with('translations')->get(),
      'pages' => Page::with('translations')->get(),
      'menus' => Menu::with(['items.translations'])->get(),
    ];

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // временная метка
    $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.json';

    Storage::disk('local')->put("backups/{$filename}", $json);

    return response($json)
      ->header('Content-Type', 'application/json')
      ->header('Content-Disposition', "attachment; filename={$filename}");
  }

  public function import(Request $request)
  {
    $request->validate([
      'backup' => 'required|file|mimes:json',
    ]);

    // 📌 Сохраняем старый бэкап перед импортом
    $this->createAutoBackup();

    $json = file_get_contents($request->file('backup')->getRealPath());
    $data = json_decode($json, true);

    if (!$data) {
      return back()->with('danger', 'Файл поврежден или пуст.');
    }

    // Чистим текущие таблицы
    Category::truncate();
    CategoryTranslation::truncate();
    Article::truncate();
    ArticleTranslation::truncate();
    Page::truncate();
    PageTranslation::truncate();
    Menu::truncate();
    MenuItem::truncate();
    MenuItemTranslation::truncate();

    // Восстанавливаем

    foreach ($data['categories'] ?? [] as $categoryData) {
      $translations = $categoryData['translations'] ?? [];
      unset($categoryData['translations']);

      $category = Category::create($categoryData);
      foreach ($translations as $t) {
        $category->translations()->create($t);
      }
    }

    foreach ($data['articles'] ?? [] as $articleData) {
      $translations = $articleData['translations'] ?? [];
      unset($articleData['translations']);

      $article = Article::create($articleData);
      foreach ($translations as $t) {
        $article->translations()->create($t);
      }
    }

    foreach ($data['pages'] ?? [] as $pageData) {
      $translations = $pageData['translations'] ?? [];
      unset($pageData['translations']);

      $page = Page::create($pageData);
      foreach ($translations as $t) {
        $page->translations()->create($t);
      }
    }

    foreach ($data['menus'] ?? [] as $menuData) {
      $items = $menuData['items'] ?? [];
      unset($menuData['items']);

      $menu = Menu::create($menuData);

      foreach ($items as $itemData) {
        $translations = $itemData['translations'] ?? [];
        unset($itemData['translations']);

        $item = $menu->items()->create($itemData);
        foreach ($translations as $t) {
          $item->translations()->create($t);
        }
      }
    }

    return back()->with('success', 'Импорт успешно выполнен.');
  }

  private function createAutoBackup()
  {
    $data = [
      'categories' => Category::with('translations')->get(),
      'articles' => Article::with('translations')->get(),
      'pages' => Page::with('translations')->get(),
      'menus' => Menu::with(['items.translations'])->get(),
    ];

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $filename = 'auto-backup_' . now()->format('Y-m-d_H-i-s') . '.json';

    Storage::disk('local')->put("backups/{$filename}", $json);
  }
}
