<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <ul class="number-grid">
    @foreach($data['items'] ?? [] as $item)
      <li class="reveal"><strong @if(! empty($item['count'])) data-count="{{ $item['count'] }}"@if(! empty($item['suffix'])) data-suffix="{{ $item['suffix'] }}"@endif @endif>@text($item['value'] ?? '')</strong><span>@text($item['label'] ?? '')</span></li>
    @endforeach
  </ul>
  <x-cms.extras :data="$data" />
</x-cms.section>
