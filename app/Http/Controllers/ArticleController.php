<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
  public function index(Request $request)
  {
    $query = Article::query()->where('status', 'published')->with('category', 'tags');

    // Фильтр по категории
    if ($request->filled('category')) {
      $query->where('category_id', $request->category);
    }

    // Фильтр по тегу
    if ($request->filled('tag')) {
      $query->whereHas('tags', fn($q) => $q->where('tags.id', $request->tag));
    }

    $articles = $query->paginate(24)->withQueryString(); // пагинация

    $categories = Category::all();
    $tags = Tag::all();

    return view('articles.index', compact('articles', 'categories', 'tags'));
  }

  public function show(string $slug)
  {
    $article = Article::where('slug', $slug)->with('category', 'tags')->firstOrFail();
    return view('articles.show', compact('article'));
  }
}
