<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="lang-grid">
    @foreach($data['items'] ?? [] as $item)
      <a class="lang-card reveal" href="{{ Cms::url($item['url'] ?? '#') }}">
        <span class="lang-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-code' }}" aria-hidden="true"></i></span>
        <h3>@text($item['name'] ?? '')</h3>
        <p class="lang-ver">@text($item['version'] ?? '')</p>
        <p class="lang-fw">@text($item['frameworks'] ?? '')</p>
        <span class="lang-link">@text($item['link_label'] ?? 'View hosting') <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
      </a>
    @endforeach
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
