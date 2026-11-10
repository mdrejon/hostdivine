@php
  $header = Cms::layout('layout_header');
  $menu = Cms::layout('menu_primary');
  $social = Cms::layout('social_links');
@endphp
<header class="site-header" id="top">
  <div class="header-strip">
    <div class="container strip-inner">
      <p><strong>@text($header['strip_brand'] ?? 'Host Divine Networks')</strong> — @text($header['strip_text'] ?? '')</p>
      <div class="strip-right">
        <div aria-label="Quick support links" class="strip-links">
          @foreach($header['strip_links'] ?? [] as $link)
            <a href="{{ Cms::url($link['url'] ?? '#') }}"@if(! empty($link['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($link['label'] ?? '')</a>
          @endforeach
        </div>
        @include('partials.social-links', ['links' => $social, 'class' => 'strip'])
      </div>
    </div>
  </div>
  <nav aria-label="Primary navigation" class="main-nav">
    <div class="container nav-inner">
      <a aria-label="{{ Cms::setting('brand_name', 'Host Divine') }} home" class="brand brand-image" href="{{ url('/') }}">
        <img alt="{{ Cms::setting('brand_name', 'Host Divine') }}" decoding="async" fetchpriority="high" height="44" loading="eager" src="{{ Cms::asset(Cms::setting('site_logo', 'assets/img/logo.svg')) }}" width="204">
      </a>
      <button aria-controls="navPanel" aria-expanded="false" aria-label="Open navigation" class="mobile-toggle" type="button">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-panel" id="navPanel">
        <ul class="nav-menu">
          @foreach($menu as $item)
            @php
              $links = collect($item['columns'] ?? [])->flatMap(fn ($column) => $column['links'] ?? []);
              $isCurrent = $links->contains(fn ($link) => Cms::isCurrent($link['url'] ?? ''));
            @endphp
            @if(! empty($item['columns']))
              <li class="nav-item has-menu{{ $isCurrent ? ' is-current' : '' }}">
                <button class="nav-link" type="button" aria-expanded="false">@text($item['label'] ?? '')</button>
                <div class="mega-menu {{ $item['layout'] ?? 'compact-menu' }}">
                  @if(! empty($item['intro_title']))
                    <div class="mega-card intro-card">
                      <span>@text($item['intro_eyebrow'] ?? '')</span>
                      <strong>@text($item['intro_title'])</strong>
                      <p>@text($item['intro_text'] ?? '')</p>
                    </div>
                  @endif
                  @foreach($item['columns'] as $column)
                    <div class="mega-column">
                      @if(! empty($column['title']))
                        <p class="mega-title">@text($column['title'])</p>
                      @endif
                      @foreach($column['links'] ?? [] as $link)
                        <a href="{{ Cms::url($link['url'] ?? '#') }}"{!! Cms::isCurrent($link['url'] ?? '') ? ' aria-current="page"' : '' !!}@if(! empty($link['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($link['label'] ?? '')@if(! empty($link['description']))<span>@text($link['description'])</span>@endif</a>
                      @endforeach
                      @if(! empty($column['promo_title']))
                        <div class="mega-promo-box">
                          <i class="{{ $column['promo_icon'] ?? 'fa-solid fa-code' }}" aria-hidden="true"></i>
                          <div><strong>@text($column['promo_title'])</strong><p>@text($column['promo_text'] ?? '')</p></div>
                        </div>
                      @endif
                    </div>
                  @endforeach
                </div>
              </li>
            @else
              <li class="nav-item{{ Cms::isCurrent($item['url'] ?? '') ? ' is-current' : '' }}">
                <a class="nav-link" href="{{ Cms::url($item['url'] ?? '#') }}"@if(Cms::isCurrent($item['url'] ?? '')) aria-current="page"@endif>@text($item['label'] ?? '')</a>
              </li>
            @endif
          @endforeach
        </ul>

        <div class="nav-actions">
          @foreach($header['actions'] ?? [] as $action)
            <a class="{{ $action['style'] ?? 'nav-login' }}" href="{{ Cms::url($action['url'] ?? '#') }}"@if(! empty($action['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($action['label'] ?? '')</a>
          @endforeach
        </div>

        @include('partials.social-links', ['links' => $social, 'class' => 'nav'])
      </div>
    </div>
  </nav>
</header>
