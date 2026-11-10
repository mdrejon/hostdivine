<x-cms.section :data="$data" :class="$data['css_class'] ?? ''" :head="false">
  <article class="blog-featured reveal">
    <div class="blog-featured-copy">
      @if(! empty($data['category']))
        <span class="blog-category-tag">@text($data['category'])</span>
      @endif
      <h2>@if(! empty($data['url']))<a href="{{ Cms::url($data['url']) }}">@text($data['title'] ?? '')</a>@else{{ Cms::text($data['title'] ?? '') }}@endif</h2>
      <p>@text($data['text'] ?? '')</p>
      @if(! empty($data['read_time']))
        <p class="blog-meta"><i class="fa-regular fa-clock" aria-hidden="true"></i> @text($data['read_time'])</p>
      @endif
    </div>
    @if(! empty($data['bars']))
      <div class="blog-featured-chart" aria-label="{{ $data['chart_label'] ?? 'Sample benchmark' }}">
        <p class="bench-title">@text($data['chart_title'] ?? '')</p>
        @foreach($data['bars'] as $bar)
          <div class="bench-row{{ ! empty($bar['best']) ? ' is-best' : '' }}"><span>@text($bar['label'] ?? '')</span><i style="--w:{{ (int) ($bar['width'] ?? 0) }}%"></i><b>@text($bar['value'] ?? '')</b></div>
        @endforeach
      </div>
    @endif
  </article>
</x-cms.section>
