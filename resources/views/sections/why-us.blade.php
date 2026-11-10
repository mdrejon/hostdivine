<section class="why-section" id="{{ $data['anchor'] ?? 'why-us' }}">
  <div class="why-shell">
    <div class="container">
      <div class="why-top">
        <div class="why-copy reveal">
          @if(! empty($data['eyebrow']))
            <span class="eyebrow is-dark"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
          @endif
          <h2 class="section-title">{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
          @if(! empty($data['points']))
            <ul class="why-list">
              @foreach($data['points'] as $point)
                <li><i class="fa-regular fa-circle-check" aria-hidden="true"></i>@text($point)</li>
              @endforeach
            </ul>
          @endif
          <x-cms.buttons :buttons="$data['buttons'] ?? []" class="hero-actions" />
        </div>
        <div class="why-cards">
          @foreach($data['cards'] ?? [] as $card)
            <article class="why-card reveal">
              <span class="why-icon"><i class="{{ $card['icon'] ?? 'fa-solid fa-star' }}" aria-hidden="true"></i></span>
              <h3>@text($card['title'] ?? '')</h3>
              <p>@text($card['text'] ?? '')</p>
              @if(! empty($card['link_label']))
                <a href="{{ Cms::url($card['link_url'] ?? '#') }}">@text($card['link_label']) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
              @endif
            </article>
          @endforeach
        </div>
      </div>
      <div class="why-divider" aria-hidden="true"></div>
      <div class="why-stats">
        @foreach($data['stats'] ?? [] as $stat)
          <div class="reveal"><strong @if(! empty($stat['count'])) data-count="{{ $stat['count'] }}"@if(! empty($stat['suffix'])) data-suffix="{{ $stat['suffix'] }}"@endif @endif>@text($stat['value'] ?? '')</strong><span>@text($stat['label'] ?? '')</span></div>
        @endforeach
      </div>
    </div>
  </div>
</section>
