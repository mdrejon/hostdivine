@if(! empty($links))
  <div class="{{ $class }}-social" aria-label="Follow {{ Cms::setting('brand_name', 'Host Divine') }}">
    @foreach($links as $link)
      <a class="{{ $class }}-social-link" href="{{ Cms::url($link['url'] ?? '#') }}" aria-label="{{ $link['label'] ?? '' }}"@if(! empty($link['new_tab'])) target="_blank" rel="noopener noreferrer"@endif><i class="{{ $link['icon'] ?? 'fa-solid fa-link' }}" aria-hidden="true"></i></a>
    @endforeach
  </div>
@endif
