@props(['button' => [], 'class' => 'btn'])
@if(! empty($button['label']))
  <a class="{{ trim($class.' '.(! empty($button['style']) ? 'btn-'.$button['style'] : '')) }}" href="{{ Cms::url($button['url'] ?? '#') }}"@if(! empty($button['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($button['label'])@if(! empty($button['icon'])) <i class="{{ $button['icon'] }}" aria-hidden="true"></i>@endif</a>
@endif
