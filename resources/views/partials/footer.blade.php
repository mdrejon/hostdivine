@php
  $footer = Cms::layout('layout_footer');
  $columns = Cms::layout('menu_footer');
@endphp
<footer class="site-footer">
  <div aria-hidden="true" class="footer-orbit"></div>
  <div class="container footer-hero">
    <div>
      <a aria-label="{{ Cms::setting('brand_name', 'Host Divine') }} home" class="brand footer-logo brand-image footer-brand-image" href="{{ url('/') }}">
        <img alt="{{ Cms::setting('brand_name', 'Host Divine') }}" decoding="async" fetchpriority="low" height="50" loading="lazy" src="{{ Cms::asset(Cms::setting('site_logo_dark', 'assets/img/dark-logo.svg')) }}" width="231">
      </a>
      <p>@text($footer['description'] ?? '')</p>
    </div>
    @if(! empty($footer['command_title']))
      <div class="footer-command">
        <span>@text($footer['command_eyebrow'] ?? '')</span>
        <strong>@text($footer['command_title'])</strong>
        <a href="{{ Cms::url($footer['command_url'] ?? '#') }}">@text($footer['command_label'] ?? 'Open Client Area')</a>
      </div>
    @endif
  </div>
  @if(! empty($footer['stats']))
    <div aria-label="{{ Cms::setting('brand_name', 'Host Divine') }} footer highlights" class="container footer-stats">
      @foreach($footer['stats'] as $stat)
        <div><strong>@text($stat['value'] ?? '')</strong><span>@text($stat['label'] ?? '')</span></div>
      @endforeach
    </div>
  @endif
  <div class="container footer-grid">
    @foreach($columns as $column)
      <div>
        <h3>@text($column['title'] ?? '')</h3>
        @foreach($column['links'] ?? [] as $link)
          <a href="{{ Cms::url($link['url'] ?? '#') }}"@if(! empty($link['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($link['label'] ?? '')</a>
        @endforeach
      </div>
    @endforeach
  </div>
  <div class="container footer-bottom">
    <p>@text($footer['copyright_prefix'] ?? '© 2004 –') <span data-current-year>{{ date('Y') }}</span> <a class="brand-footer-link" href="{{ url('/') }}">@text($footer['copyright_brand'] ?? 'Host Divine™')</a> @text($footer['copyright_suffix'] ?? '')</p>
    <div class="footer-bottom-links">
      @foreach($footer['bottom_links'] ?? [] as $link)
        <a href="{{ Cms::url($link['url'] ?? '#') }}"@if(! empty($link['new_tab'])) target="_blank" rel="noopener noreferrer"@endif><i aria-hidden="true" class="{{ $link['icon'] ?? 'fa-solid fa-link' }}"></i><span>@text($link['label'] ?? '')</span></a>
      @endforeach
    </div>
  </div>
</footer>
