<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="topic-grid">
    @foreach($data['items'] ?? [] as $item)
      <a class="topic-card reveal" href="{{ Cms::url($item['url'] ?? '#') }}"><span class="highlight-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-circle-info' }}" aria-hidden="true"></i></span><span><strong>@text($item['title'] ?? '')</strong><small>@text($item['count'] ?? '')</small></span><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
    @endforeach
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
