@php($slides = $data['slides'] ?? [])
<section class="hero-slider" id="{{ $data['anchor'] ?? 'hero' }}" aria-roledescription="carousel" aria-label="{{ $data['label'] ?? 'Highlights' }}">
  <div class="hero-bg" aria-hidden="true">
    @foreach($slides as $slide)
      <div class="hero-bg-image hero-bg-img-{{ ($loop->index % 4) + 1 }}{{ $loop->first ? ' is-active' : '' }}"></div>
    @endforeach
    <span class="hero-grid"></span>
    <span class="hero-glow glow-a"></span>
    <span class="hero-glow glow-b"></span>
  </div>

  <div class="hero-viewport" data-hero-viewport>
    @foreach($slides as $slide)
      <article class="hero-slide{{ $loop->first ? ' is-active' : '' }}" role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $loop->count }}">
        <div class="container hero-inner">
          <div class="hero-copy">
            @if(! empty($slide['eyebrow']))
              <span class="hero-eyebrow"><span class="eyebrow-dot"></span>@text($slide['eyebrow'])</span>
            @endif
            @if($loop->first)
              <h1 class="hero-title">{{ Cms::highlight($slide['title'] ?? '', $slide['title_highlight'] ?? null) }}</h1>
            @else
              <h2 class="hero-title">{{ Cms::highlight($slide['title'] ?? '', $slide['title_highlight'] ?? null) }}</h2>
            @endif
            @if(! empty($slide['text']))
              <p class="hero-text">@text($slide['text'])</p>
            @endif
            @if(! empty($slide['points']))
              <ul class="hero-points">
                @foreach($slide['points'] as $point)
                  <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>@text($point)</li>
                @endforeach
              </ul>
            @endif
            <x-cms.buttons :buttons="$slide['buttons'] ?? []" class="hero-actions" />
            @if(! empty($slide['price']))
              <p class="hero-price">@text($slide['price_label'] ?? 'Starting at') <strong>@text($slide['price'])</strong><span>@text($slide['period'] ?? '')</span></p>
            @endif
          </div>
          @include('sections.partials.hero-visual', ['slide' => $slide, 'first' => $loop->first])
        </div>
      </article>
    @endforeach
  </div>

  <div class="container hero-controls">
    <button class="hero-arrow" type="button" data-hero-prev aria-label="Previous slide"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
    <div class="hero-tabs" role="tablist" aria-label="Choose slide">
      @foreach($slides as $slide)
        <button class="hero-tab{{ $loop->first ? ' is-active' : '' }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-hero-goto="{{ $loop->index }}"><span>@text($slide['tab_label'] ?? 'Slide '.$loop->iteration)</span><i class="hero-progress"></i></button>
      @endforeach
    </div>
    <button class="hero-arrow" type="button" data-hero-next aria-label="Next slide"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
  </div>
</section>
