<section class="domain-search-section" id="{{ $data['anchor'] ?? 'domain-search' }}" aria-labelledby="ds-title">
  <div class="container">
    <div class="ds-head">
      @if(! empty($data['badge']))
        <span class="ds-badge"><span class="eyebrow-dot"></span>@text($data['badge'])@if(! empty($data['badge_highlight'])) <span class="ds-badge-sep" aria-hidden="true">•</span> <b>@text($data['badge_highlight'])</b>@endif</span>
      @endif
      <h2 class="ds-title" id="ds-title">{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
      @if(! empty($data['subtitle']))
        <p class="ds-sub">@text($data['subtitle'])</p>
      @endif
    </div>
    <x-cms.domain-search :mode="$data['mode'] ?? 'register'" prefix="home" />
  </div>
</section>
