<section class="{{ trim('section services-section '.($data['background'] ?? '')) }}" id="{{ $data['anchor'] ?? 'services' }}">
  <div class="container">
    <div class="services-shell reveal">
      <div class="services-col-intro">
        <div class="services-intro-header">
          @if(! empty($data['eyebrow']))
            <span class="eyebrow-pill"><i class="{{ $data['eyebrow_icon'] ?? 'fa-solid fa-bars-staggered' }}" aria-hidden="true"></i><span>@text($data['eyebrow'])</span></span>
          @endif
          <h2 class="services-headline">{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
          @if(! empty($data['subtitle']))
            <p class="services-lead">@text($data['subtitle'])</p>
          @endif
        </div>
        <div class="feature-dark-stack">
          @foreach($data['cards'] ?? [] as $card)
            <div class="feature-dark-card">
              <div class="feature-dark-content"><h3>@text($card['title'] ?? '')</h3><p>@text($card['text'] ?? '')</p></div>
              <div class="feature-dark-icon" aria-hidden="true"><i class="{{ $card['icon'] ?? 'fa-solid fa-shield-halved' }}"></i></div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="services-col-cards">
        <div class="platform-included-banner">
          <span class="platform-banner-title">@text($data['banner_title'] ?? '')</span>
          <div class="platform-tags-list">
            @foreach($data['tags'] ?? [] as $tag)
              <span class="platform-tag">@text($tag)</span>
            @endforeach
          </div>
        </div>
        <div class="platform-services-grid">
          @foreach($data['services'] ?? [] as $service)
            <div class="platform-svc-card">
              <h4>@text($service['title'] ?? '')</h4>
              <p>@text($service['text'] ?? '')</p>
              @if(! empty($service['link_label']))
                <a class="platform-svc-link" href="{{ Cms::url($service['link_url'] ?? '#') }}">@text($service['link_label']) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
