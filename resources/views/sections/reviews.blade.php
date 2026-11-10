<x-cms.section :data="$data" :class="$data['css_class'] ?? 'reviews-section'">
  <div class="card-slider per-3" data-slider>
    <div class="slider-track" data-slider-track tabindex="0" aria-label="{{ $data['slider_label'] ?? 'Customer reviews' }}">
      @foreach($data['items'] ?? [] as $review)
        @php($rating = max(0, min(5, (int) ($review['rating'] ?? 5))))
        <article class="review-card">
          <div class="review-stars" aria-label="Rated {{ $rating }} out of 5">
            @for($star = 1; $star <= 5; $star++)
              <i class="{{ $star <= $rating ? 'fa-solid' : 'fa-regular' }} fa-star" aria-hidden="true"></i>
            @endfor
          </div>
          <h3>@text($review['title'] ?? '')</h3>
          <p>@text($review['text'] ?? '')</p>
          <div class="review-author">
            <span class="avatar is-{{ $review['avatar_color'] ?? 'blue' }}">@text($review['initials'] ?? '')</span>
            <div><strong>@text($review['name'] ?? '')</strong><small>@text($review['role'] ?? '')</small></div>
            @if(! empty($review['tag']))
              <span class="review-tag">@text($review['tag'])</span>
            @endif
          </div>
        </article>
      @endforeach
    </div>
    @include('sections.partials.slider-nav', ['navLabel' => $data['nav_label'] ?? 'reviews'])
  </div>
</x-cms.section>
