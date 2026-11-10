<x-cms.section :data="$data" :class="$data['css_class'] ?? ''" :head="false">
  <div class="framework-row reveal">
    <p>@text($data['label'] ?? 'Works with')</p>
    <ul>
      @foreach($data['items'] ?? [] as $item)
        <li>@text($item)</li>
      @endforeach
    </ul>
  </div>
</x-cms.section>
