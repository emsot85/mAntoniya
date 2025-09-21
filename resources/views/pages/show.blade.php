@extends('layouts.app')
@section('content')
<article class="page-content">
    @php
        $currentLocale = app()->getLocale();
        $fallbackLocale = 'en'; // Локаль для резервного варианта      
    @endphp

   <h1 class="text-gray-800 gap-4 text-3xl pb-3">
        {{ $page->translate($currentLocale)->title ?? $page->translate($fallbackLocale)->title }}
    </h1>

    @if($page->translate($currentLocale)->extra_field_1 ?? $page->translate($fallbackLocale)->extra_field_1)
    <div class="extra-field extra-field-1 gap-4 pb-3">
        {!! Purifier::clean($page->translate($currentLocale)->extra_field_1 ?? $page->translate($fallbackLocale)->extra_field_1, 'default') !!}
    </div>
    @endif

    @if($page->translate($currentLocale)->description ?? $page->translate($fallbackLocale)->description)
    <div class="description gap-4 pb-3">
        {!! Purifier::clean($page->translate($currentLocale)->description ?? $page->translate($fallbackLocale)->description, 'default') !!}
    </div>
    @endif

    @if($page->translate($currentLocale)->extra_field_2 ?? $page->translate($fallbackLocale)->extra_field_2)
    <div class="extra-field extra-field-2 gap-4 pb-3">
        {!! Purifier::clean($page->translate($currentLocale)->extra_field_2 ?? $page->translate($fallbackLocale)->extra_field_2, 'default') !!}
    </div>
    @endif

    @if($page->translate($currentLocale)->content ?? $page->translate($fallbackLocale)->content)
    <div class="content gap-4 pb-3">
        {!! Purifier::clean($page->translate($currentLocale)->content ?? $page->translate($fallbackLocale)->content, 'default') !!}
    </div>
    @endif

    @if($page->translate($currentLocale)->extra_field_3 ?? $page->translate($fallbackLocale)->extra_field_3)
    <div class="extra-field extra-field-3 gap-4 pb-3">
        {!! Purifier::clean($page->translate($currentLocale)->extra_field_3 ?? $page->translate($fallbackLocale)->extra_field_3, 'default') !!}
    </div>
    @endif
</article>

{{-- SEO поля для мета-тегов --}}
@push('meta')
<title>
    {{ $page->translate($currentLocale)->meta_title 
        ?? $page->translate($fallbackLocale)->meta_title 
        ?? $page->translate($currentLocale)->title 
        ?? $page->translate($fallbackLocale)->title }}
</title>
<meta name="description" content="{{ $page->translate($currentLocale)->meta_description ?? $page->translate($fallbackLocale)->meta_description ?? '' }}">
@endpush
@endsection

