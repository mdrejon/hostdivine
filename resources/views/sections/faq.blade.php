@php($help = Cms::layout('widget_faq_help'))
<section class="section {{ $data['background'] ?? '' }}"@if(! empty($data['anchor'])) id="{{ $data['anchor'] }}"@endif>
  <div class="container faq-layout">
    <div class="faq-intro reveal">
      @if(! empty($data['eyebrow']))
        <span class="eyebrow"><span class="eyebrow-dot"></span>@text($data['eyebrow'])</span>
      @endif
      <h2 class="section-title">{{ Cms::highlight($data['title'] ?? '', $data['title_highlight'] ?? null) }}</h2>
      @if(! empty($data['subtitle']))
        <p class="section-sub">@text($data['subtitle'])</p>
      @endif
      @if(! empty($help['title']))
        <div class="faq-help">
          <span class="faq-help-icon"><i class="{{ $help['icon'] ?? 'fa-solid fa-headset' }}" aria-hidden="true"></i></span>
          <div><strong>@text($help['title'])</strong><p>@text($help['text'] ?? '')</p></div>
        </div>
      @endif
      @if(! empty($help['button_label']))
        <a class="btn btn-outline" href="{{ Cms::url($help['button_url'] ?? '/contact') }}">@text($help['button_label']) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
      @endif
    </div>
    <div class="reveal">
      <x-cms.faq-accordion :items="$data['items'] ?? []" />
    </div>
  </div>
</section>
