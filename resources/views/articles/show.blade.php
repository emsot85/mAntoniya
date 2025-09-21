@extends('layouts.app')

@section('title', $article->translate(app()->getLocale())->meta_title ?? $article->translate(app()->getLocale())->title)
@section('description', $article->translate(app()->getLocale())->meta_description)

@section('content')
<div class="container mx-auto px-4 py-6">

  {{-- Заголовок --}}
  <h1 class="text-3xl font-bold mb-4">
    {{ $article->translate(app()->getLocale())->title }}
  </h1>

  {{-- Автор, дата, просмотры и лайки --}}
  <div class="text-gray-500 mb-4">
    @if($article->author)
    <span>Автор: {{ $article->author->name }}</span> |
    @endif
    @if($article->published_at)
    <span>Опубликовано: {{ $article->published_at->format('d.m.Y') }}</span>
    @endif
    <!-- <span>Просмотры: {{ $article->views_count }}</span> |
    <span>Лайки: {{ $article->likes_count }}</span> -->
  </div>

  {{-- Картинка --}}
  @if($article->image)
  <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->translate(app()->getLocale())->title }}" class="mb-6 w-full max-h-96 object-cover rounded">
  @endif

  {{-- Основное описание --}}
  @if($article->translate(app()->getLocale())->description)
  <div class="prose max-w-full mb-6">
    {!! $article->translate(app()->getLocale())->description !!}
  </div>
  @endif

  {{-- Дополнительные поля --}}
  @foreach(['extra_field_1','extra_field_2','extra_field_3'] as $field)
  @if($article->translate(app()->getLocale())->$field)
  <div class="prose max-w-full mb-6">
    {!! $article->translate(app()->getLocale())->$field !!}
  </div>
  @endif
  @endforeach

  {{-- Видео --}}
  @if($article->videos)
  <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach(json_decode($article->videos, true) as $video)
    @if(!empty($video['url']))
    <iframe class="w-full h-60" src="{{ $video['url'] }}" frameborder="0" allowfullscreen></iframe>
    @endif
    @endforeach
  </div>
  @endif

  {{-- Кнопки --}}
  @if($article->buttons)
  <div class="flex gap-2 flex-wrap mb-6">
    @foreach(json_decode($article->buttons, true) as $button)
    <a href="{{ $button['url'] ?? '#' }}" class="px-4 py-2 rounded text-white font-medium" style="background-color: {{ $button['color'] ?? '#3b82f6' }}">
      {{ $button['title'] ?? 'Ссылка' }}
    </a>
    @endforeach
  </div>
  @endif

</div>
@endsection