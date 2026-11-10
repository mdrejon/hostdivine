<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="specs-grid reveal">
    @foreach($data['groups'] ?? [] as $group)
      <div class="spec-group">
        <h3 class="spec-group-title"><i class="{{ $group['icon'] ?? 'fa-solid fa-server' }}" aria-hidden="true"></i> @text($group['title'] ?? '')</h3>
        @foreach($group['rows'] ?? [] as $row)
          <div class="spec-row"><span class="spec-label">@text($row['label'] ?? '')</span><span class="spec-value">@text($row['value'] ?? '')</span></div>
        @endforeach
      </div>
    @endforeach
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
