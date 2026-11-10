<section class="cta-section">
  <div class="container">
    <div class="page-cta-banner reveal">
      <div class="page-cta-bg" aria-hidden="true"></div>
      <div class="page-cta-copy">
        @if(! empty($data['eyebrow']))
          <span class="eyebrow is-dark"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
        @endif
        <h2>{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
        @if(! empty($data['text']))
          <p>@text($data['text'])</p>
        @endif
      </div>
      <x-cms.buttons :buttons="$data['buttons'] ?? []" class="page-cta-actions" />
    </div>
  </div>
</section>
