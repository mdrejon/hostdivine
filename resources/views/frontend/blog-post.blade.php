@extends('layouts.frontend')

@section('meta_title', $post->meta_title ?: $post->title.' | '.Cms::setting('brand_name', 'Host Divine').'™ Blog')
@section('meta_description', (string) ($post->meta_description ?: $post->excerpt))
@section('preload_image', \App\Services\SeoService::HERO_IMAGES['1'])
@section('hero_background', '1')
@if($post->featured_image)
  @section('og_image', $post->featured_image)
@endif

@push('schema')
  <script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'datePublished' => ($post->published_at ?? $post->created_at)?->toAtomString(),
    'dateModified' => $post->updated_at?->toAtomString(),
    'author' => ['@type' => 'Person', 'name' => $post->author_name],
    'mainEntityOfPage' => url()->current(),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
  @include('sections.page-hero', ['page' => $post, 'data' => [
    'breadcrumbs' => [['label' => 'Company'], ['label' => 'Blog', 'url' => '/blog']],
    'breadcrumb_current' => \Illuminate\Support\Str::limit($post->title, 40),
    'eyebrow' => $post->category ?: 'Article',
    'title' => $post->title,
    'lead' => $post->excerpt,
    'badges' => [
      ['icon' => 'fa-regular fa-user', 'text' => $post->author_name],
      ['icon' => 'fa-regular fa-calendar', 'text' => ($post->published_at ?? $post->created_at)->format('M d, Y')],
      ['icon' => 'fa-regular fa-clock', 'text' => $post->read_time],
    ],
  ]])

  <section class="section bg-soft">
    <div class="container article-wrap">
      @if($post->featured_image)
        <img class="article-cover" src="{{ Cms::asset($post->featured_image) }}" alt="{{ $post->title }}" width="1200" height="630" decoding="async">
      @endif
      <article class="article-body">{!! $post->content !!}</article>
      <div class="article-foot">
        <a class="btn btn-outline" href="{{ url('/blog') }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to Blog</a>
      </div>
    </div>
  </section>

  @if($recentPosts->isNotEmpty())
    <section class="section bg-grid">
      <div class="container">
        <div class="section-head reveal">
          <span class="eyebrow"><span class="eyebrow-dot"></span>Related</span>
          <h2 class="section-title">More articles to read</h2>
        </div>
        <div class="blog-grid">
          @foreach($recentPosts as $recent)
            <article class="blog-card reveal">
              <div class="blog-thumb-wrap"><i class="{{ $recent->icon }}" aria-hidden="true"></i></div>
              <div class="blog-body">
                @if($recent->category)
                  <span class="blog-category-tag">{{ $recent->category }}</span>
                @endif
                <h3><a href="{{ route('blog.show', $recent->slug) }}">{{ $recent->title }}</a></h3>
                <p>{{ $recent->excerpt }}</p>
                <p class="blog-meta"><span><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ ($recent->published_at ?? $recent->created_at)->format('M d, Y') }}</span><span><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ $recent->read_time }}</span></p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
