@props(['plan' => []])
@if(($plan['kind'] ?? 'plan') === 'cta')
  <article class="plan-card plan-card-cta">
    <span class="cta-icon"><i class="{{ $plan['icon'] ?? 'fa-solid fa-cloud' }}" aria-hidden="true"></i></span>
    <h3 class="plan-name">@text($plan['name'] ?? '')</h3>
    <p class="plan-desc">@text($plan['description'] ?? '')</p>
    @if(! empty($plan['links']))
      <ul class="cta-links">
        @foreach($plan['links'] as $link)
          <li><a href="{{ Cms::url($link['url'] ?? '#') }}">@text($link['label'] ?? '') <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
        @endforeach
      </ul>
    @endif
    @if(! empty($plan['button_label']))
      <a class="btn btn-light plan-btn" href="{{ Cms::url($plan['button_url'] ?? '#') }}">@text($plan['button_label'])</a>
    @endif
  </article>
@else
  @php($featured = ! empty($plan['featured']))
  <article class="plan-card{{ $featured ? ' is-featured' : '' }}">
    <div class="plan-top">
      <span class="plan-cat{{ ! empty($plan['category_style']) ? ' '.$plan['category_style'] : '' }}">@text($plan['category'] ?? '')</span>
      @if(! empty($plan['badge']))
        <span class="plan-badge{{ ! empty($plan['badge_style']) ? ' '.$plan['badge_style'] : '' }}">@text($plan['badge'])</span>
      @endif
    </div>
    <h3 class="plan-name">@text($plan['name'] ?? '')</h3>
    @if(! empty($plan['description']))
      <p class="plan-desc">@text($plan['description'])</p>
    @endif
    <div class="plan-price"><sup>@text($plan['currency'] ?? '$')</sup><span>@text($plan['price'] ?? '')</span><small>@text($plan['period'] ?? '')</small></div>
    @if(! empty($plan['billed']))
      <p class="plan-billed">@text($plan['billed'])</p>
    @endif
    @if(! empty($plan['button_label']))
      <a class="btn {{ $featured ? 'btn-primary' : 'btn-outline' }} plan-btn" href="{{ Cms::url($plan['button_url'] ?? '#') }}">@text($plan['button_label'])</a>
    @endif
    @if(! empty($plan['features']))
      <ul class="plan-features">
        @foreach($plan['features'] as $feature)
          {{-- A feature starting with "- " is shown as not included. --}}
          @if(str_starts_with((string) $feature, '- '))
            <li class="is-off"><i class="fa-solid fa-xmark" aria-hidden="true"></i>@text(substr($feature, 2))</li>
          @else
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>@text($feature)</li>
          @endif
        @endforeach
      </ul>
    @endif
  </article>
@endif
