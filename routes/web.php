<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BackupController;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/articles', [\App\Http\Controllers\ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('articles.show');
Route::get('/backup/export', [BackupController::class, 'export'])->name('backup.export');
Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');

Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

Route::get('locale/{locale}', function ($locale) {
  if (in_array($locale, config('translatable.locales'))) {
    session(['locale' => $locale]);
    app()->setLocale($locale);
  }

  return redirect()->back();
})->name('locale.switch');
