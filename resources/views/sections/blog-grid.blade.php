@php
  $posts = \App\Models\BlogPost::query()
      ->published()
      ->when(! empty($data['exclude']), fn ($query) => $query->whereNotIn('slug', (array) $data['exclude']))
      ->take((int) ($data['limit'] ?? 9))
      ->get();
@endphp
<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="blog-grid">
    @foreach($posts as $post)
      <article class="blog-card reveal">
        <div class="blog-thumb-wrap">
          @if($post->featured_image)
            <img src="{{ Cms::asset($post->featured_image) }}" alt="{{ $post->title }}" width="640" height="360" loading="lazy" decoding="async">
          @else
            <i class="{{ $post->icon }}" aria-hidden="true"></i>
          @endif
        </div>
        <div class="blog-body">
          @if($post->category)
            <span class="blog-category-tag">{{ $post->category }}</span>
          @endif
          <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
          <p>{{ $post->excerpt }}</p>
          <p class="blog-meta"><span><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span><span><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ $post->read_time }}</span></p>
        </div>
      </article>
    @endforeach
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
