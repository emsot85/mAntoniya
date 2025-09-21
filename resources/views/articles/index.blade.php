@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
  {{-- Фильтры --}}
  <div class="flex gap-4 mb-6">
    <form method="GET" action="{{ route('articles.index') }}" class="flex gap-2">
      <!-- <select name="category" onchange="this.form.submit()" class="border rounded px-2 py-1">
        <option value="">---</option>
        @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected(request('category')==$category->id)>
          {{ $category->translate(app()->getLocale())->title ?? $category->translate('en')->title }}
        </option>
        @endforeach
      </select> -->
      <!-- <select name="tag" onchange="this.form.submit()" class="border rounded px-2 py-1">
        <option value="">Все теги</option>
        @foreach($tags as $tag)
        <option value="{{ $tag->id }}" @selected(request('tag')==$tag->id)>
          {{ $tag->translate(app()->getLocale())->title ?? $tag->translate('en')->title }}
        </option>
        @endforeach
      </select> -->
    </form>
  </div>

  {{-- Сетка карточек --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($articles as $article)
    @php
      $currentLocale = app()->getLocale();
      $fallbackLocale = 'en';
    @endphp
    <div class="bg-white shadow rounded overflow-hidden">
      @if($article->image)
      <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->translate($currentLocale)->title ?? $article->translate($fallbackLocale)->title }}" class="w-full h-40 object-cover">
      @endif
      <div class="p-4">
        <h3 class="font-bold text-lg">
          <a href="{{ route('articles.show', $article->slug) }}" class="mt-3 inline-block text-blue-600 hover:underline">
            {{ $article->translate($currentLocale)->title ?? $article->translate($fallbackLocale)->title }}
          </a>  
        </h3>
        <p class="text-gray-600 mt-2">
          {!! Str::limit(
            $article->translate($currentLocale)->description ??
            $article->translate($fallbackLocale)->description ??
            ($article->translate($currentLocale)->content ??
            $article->translate($fallbackLocale)->content),
            100
          ) !!}
        </p>
        <a href="{{ route('articles.show', $article->slug) }}" class="mt-3 inline-block text-blue-600 hover:underline">
          {{ __('messages.read_more') }}
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Пагинация --}}
  <div class="mt-6">
    {{ $articles->links() }}
  </div>
</div>
@endsection
