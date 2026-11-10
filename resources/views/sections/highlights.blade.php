<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  @if(! empty($data['items']))
    <div class="highlights-grid cols-{{ $data['columns'] ?? 3 }}">
      @foreach($data['items'] as $item)
        <article class="highlight-card reveal">
          <div class="highlight-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-star' }}" aria-hidden="true"></i></div>
          <h3>@text($item['title'] ?? '')</h3>
          <p>@text($item['text'] ?? '')</p>
        </article>
      @endforeach
    </div>
  @endif
  <x-cms.extras :data="$data" />
</x-cms.section>
