@props(['data' => []])
@if(! empty($data['title']) || ! empty($data['eyebrow']))
  <div class="section-head reveal">
    @if(! empty($data['eyebrow']))
      <span class="eyebrow{{ ! empty($data['eyebrow_dark']) ? ' is-dark' : '' }}"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
    @endif
    @if(! empty($data['title']))
      <h2 class="section-title">{{ Cms::highlight($data['title'], $data['title_highlight'] ?? null) }}</h2>
    @endif
    @if(! empty($data['subtitle']))
      <p class="section-sub">@text($data['subtitle'])</p>
    @endif
  </div>
@endif
