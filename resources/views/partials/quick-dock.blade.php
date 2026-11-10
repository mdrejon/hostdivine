@php($dock = Cms::layout('widget_quick_dock'))
<aside class="quick-dock" aria-label="Quick contact and navigation">
  <a class="dock-btn dock-top" href="#top" aria-label="Scroll to top of page" title="Scroll to Top"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i><span class="dock-tooltip">Scroll to Top</span></a>
  @foreach($dock['buttons'] ?? [] as $button)
    <a class="dock-btn {{ $button['style'] ?? 'dock-support' }}" href="{{ Cms::url($button['url'] ?? '#') }}"@if(! empty($button['new_tab'])) target="_blank" rel="noopener noreferrer"@endif aria-label="{{ $button['label'] ?? '' }}" title="{{ $button['title'] ?? ($button['label'] ?? '') }}"><i class="{{ $button['icon'] ?? 'fa-solid fa-headset' }}" aria-hidden="true"></i><span class="dock-tooltip">@text($button['tooltip'] ?? ($button['label'] ?? ''))</span></a>
  @endforeach
</aside>
