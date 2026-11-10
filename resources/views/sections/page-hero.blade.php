@php
  $hasConsole = ! empty($data['console_rows']);
  $centered = ! empty($data['centered']);
  $searchMode = $data['domain_search'] ?? '';
@endphp
<section class="page-hero{{ $centered ? ' is-center' : '' }}"@if(! empty($data['anchor'])) id="{{ $data['anchor'] }}"@endif>
  <div class="page-hero-bg" aria-hidden="true"><span class="page-hero-grid-lines"></span><span class="page-hero-glow"></span></div>
  <div class="container">
    <nav aria-label="Breadcrumb">
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}">Home</a></li>
        @foreach($data['breadcrumbs'] ?? [] as $crumb)
          <li>@if(! empty($crumb['url']))<a href="{{ Cms::url($crumb['url']) }}">@text($crumb['label'] ?? '')</a>@else<span>@text($crumb['label'] ?? '')</span>@endif</li>
        @endforeach
        <li aria-current="page">@text($data['breadcrumb_current'] ?? $page->title)</li>
      </ol>
    </nav>
    <div class="page-hero-grid{{ $hasConsole ? '' : ' is-single' }}">
      <div class="page-hero-copy">
        @if(! empty($data['eyebrow']))
          <span class="eyebrow is-dark"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
        @endif
        <h1 class="page-hero-title">{{ Cms::highlight($data['title'] ?? $page->title, $data['title_highlight'] ?? null) }}</h1>
        @if(! empty($data['lead']))
          <p class="page-hero-lead">@text($data['lead'])</p>
        @endif
        @if($searchMode)
          <x-cms.domain-search :mode="$searchMode" :prefix="$searchMode === 'transfer' ? 'tr' : 'reg'" />
        @endif
        <x-cms.buttons :buttons="$data['buttons'] ?? []" class="page-hero-actions" />
        @if(! empty($data['badges']))
          <ul class="page-hero-badges">
            @foreach($data['badges'] as $badge)
              <li><i class="{{ $badge['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i>@text($badge['text'] ?? '')</li>
            @endforeach
          </ul>
        @endif
      </div>
      @if($hasConsole)
        <div class="page-hero-visual" aria-label="Platform summary">
          <div class="hero-console-head">
            <div class="hero-console-dots" aria-hidden="true"><span></span><span></span><span></span></div>
            <span class="hero-console-badge"><i class="fa-solid fa-circle" aria-hidden="true"></i> @text($data['console_badge'] ?? '')</span>
          </div>
          @foreach($data['console_rows'] as $row)
            <div class="hero-console-row">
              <span class="hero-console-label"><i class="{{ $row['icon'] ?? 'fa-solid fa-server' }}" aria-hidden="true"></i> @text($row['label'] ?? '')</span>
              <span class="hero-console-val">@text($row['value'] ?? '')</span>
            </div>
          @endforeach
          @if(! empty($data['console_price']))
            <div class="hero-console-foot">
              <p>@text($data['console_price_label'] ?? 'Starting at') <strong>@text($data['console_price'])</strong><span>@text($data['console_period'] ?? '')</span></p>
              @if(! empty($data['console_button_label']))
                <a class="btn btn-primary" href="{{ Cms::url($data['console_button_url'] ?? '#plans') }}">@text($data['console_button_label'])</a>
              @endif
            </div>
          @endif
        </div>
      @endif
    </div>
  </div>
</section>
