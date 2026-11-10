<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <ol class="steps-grid">
    @foreach($data['items'] ?? [] as $item)
      <li class="step-card reveal">
        <span class="step-num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
        <span class="step-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
        <h3>@text($item['title'] ?? '')</h3>
        <p>@text($item['text'] ?? '')</p>
      </li>
    @endforeach
  </ol>
  <x-cms.extras :data="$data" />
</x-cms.section>
