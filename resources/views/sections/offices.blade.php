<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="office-grid">
    @foreach($data['items'] ?? [] as $item)
      <article class="office-card reveal">
        <span class="highlight-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-building' }}" aria-hidden="true"></i></span>
        <h3>@text($item['title'] ?? '')</h3>
        <p>{{ Cms::lines($item['address'] ?? '') }}</p>
      </article>
    @endforeach
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
