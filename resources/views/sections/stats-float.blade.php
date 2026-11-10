<div class="stats-float-wrap">
  <div class="container">
    <ul class="stats-float">
      @foreach($data['items'] ?? [] as $item)
        <li><span class="stats-float-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-star' }}" aria-hidden="true"></i></span><div><strong>@text($item['value'] ?? '')</strong><span>@text($item['label'] ?? '')</span></div></li>
      @endforeach
    </ul>
  </div>
</div>
