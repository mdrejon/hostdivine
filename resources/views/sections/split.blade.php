<x-cms.section :data="$data" :class="$data['css_class'] ?? ''" :head="false">
  <div class="split-block">
    <div class="split-copy reveal">
      @if(! empty($data['eyebrow']))
        <span class="eyebrow{{ ! empty($data['eyebrow_dark']) ? ' is-dark' : '' }}"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
      @endif
      <h2 class="section-title">{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
      @if(! empty($data['subtitle']))
        <p class="section-sub">@text($data['subtitle'])</p>
      @endif
      @if(! empty($data['checklist']))
        <ul class="check-list">
          @foreach($data['checklist'] as $point)
            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>@text($point)</li>
          @endforeach
        </ul>
      @endif
      <x-cms.buttons :buttons="$data['buttons'] ?? []" class="split-actions" />
    </div>
    <div class="split-aside reveal">
      <div class="info-card{{ ! empty($data['card_dark']) ? ' is-dark' : '' }}">
        <span class="info-card-icon"><i class="{{ $data['card_icon'] ?? 'fa-solid fa-circle-info' }}" aria-hidden="true"></i></span>
        <h3>@text($data['card_title'] ?? '')</h3>
        @if(! empty($data['card_text']))
          <p>@text($data['card_text'])</p>
        @endif
        @if(! empty($data['card_items']))
          @if(($data['card_list'] ?? 'price') === 'price')
            <ul class="price-list">
              @foreach($data['card_items'] as $item)
                <li><span>@text($item['label'] ?? '')</span><b>@text($item['value'] ?? '')</b></li>
              @endforeach
            </ul>
          @else
            <ul class="info-card-list">
              @foreach($data['card_items'] as $item)
                <li><i class="{{ $item['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i>{{ Cms::rich($item['label'] ?? '') }}</li>
              @endforeach
            </ul>
          @endif
        @endif
      </div>
    </div>
  </div>
</x-cms.section>
