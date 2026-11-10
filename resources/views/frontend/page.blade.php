@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?: $page->title.' | '.Cms::setting('brand_name', 'Host Divine').'™')
@section('meta_description', (string) $page->meta_description)
@if($page->meta_keywords)
  @section('meta_keywords', $page->meta_keywords)
@endif
@if($page->og_image)
  @section('og_image', $page->og_image)
@endif
@if($preloadImage)
  @section('preload_image', $preloadImage)
@endif
@if($page->hero_background)
  @section('hero_background', $page->hero_background)
@endif

@push('schema')
  @foreach($schemas as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  @endforeach
@endpush

@section('content')
  @foreach($sections as $section)
    @includeIf('sections.'.str_replace('_', '-', $section->renderType()), ['data' => $section->renderData(), 'section' => $section, 'page' => $page])
  @endforeach
@endsection
