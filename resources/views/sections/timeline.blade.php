<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <ol class="timeline-list">
    @foreach($data['items'] ?? [] as $item)
      <li class="timeline-node reveal">
        <span class="timeline-dot"><i class="{{ $item['icon'] ?? 'fa-solid fa-flag' }}" aria-hidden="true"></i></span>
        <div class="timeline-card">
          <span class="timeline-year">@text($item['year'] ?? '')</span>
          <h3>@text($item['title'] ?? '')</h3>
          <p>@text($item['text'] ?? '')</p>
        </div>
      </li>
    @endforeach
  </ol>
  <x-cms.extras :data="$data" />
</x-cms.section>
