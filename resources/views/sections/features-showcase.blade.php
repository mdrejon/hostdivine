<x-cms.section :data="$data" :class="$data['css_class'] ?? 'features-section'">
  <div class="features-grid">
    @foreach($data['cards'] ?? [] as $card)
      <article class="feature-card reveal">
        <img src="{{ Cms::asset($card['image'] ?? '') }}" alt="{{ $card['alt'] ?? '' }}" width="360" height="96" loading="lazy" decoding="async">
        <p>@text($card['text'] ?? '')</p>
      </article>
    @endforeach
  </div>
  @if(! empty($data['badges']))
    <ul class="feature-badges reveal" aria-label="{{ $data['badges_label'] ?? 'Also included' }}">
      @foreach($data['badges'] as $badge)
        <li><i class="{{ $badge['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i>@text($badge['text'] ?? '')</li>
      @endforeach
    </ul>
  @endif
  @if(! empty($data['langs']))
    <div class="platform-langs reveal">
      <div class="platform-langs-head">
        <div>
          @if(! empty($data['langs_eyebrow']))
            <span class="eyebrow"><span class="eyebrow-dot"></span>@text($data['langs_eyebrow'])</span>
          @endif
          <h3>@text($data['langs_title'] ?? '')</h3>
        </div>
        @if(! empty($data['langs_link_label']))
          <a class="platform-langs-all" href="{{ Cms::url($data['langs_link_url'] ?? '/languages') }}">@text($data['langs_link_label']) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        @endif
      </div>
      <div class="lang-chip-grid">
        @foreach($data['langs'] as $lang)
          <a class="lang-chip{{ ! empty($lang['style']) ? ' '.$lang['style'] : '' }}" href="{{ Cms::url($lang['url'] ?? '#') }}"><span class="lang-chip-icon"><i class="{{ $lang['icon'] ?? 'fa-solid fa-code' }}" aria-hidden="true"></i></span><span class="lang-chip-text"><strong>@text($lang['name'] ?? '')</strong><small>@text($lang['meta'] ?? '')</small></span></a>
        @endforeach
      </div>
    </div>
  @endif
</x-cms.section>
