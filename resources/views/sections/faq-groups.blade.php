<section class="section {{ $data['background'] ?? '' }}"@if(! empty($data['anchor'])) id="{{ $data['anchor'] }}"@endif>
  <div class="container faq-groups">
    @foreach($data['groups'] ?? [] as $group)
      <div class="faq-group"@if(! empty($group['anchor'])) id="{{ $group['anchor'] }}"@endif>
        <div class="faq-group-head reveal">
          @if(! empty($group['eyebrow']))
            <span class="eyebrow"><span class="eyebrow-dot"></span>@text($group['eyebrow'])</span>
          @endif
          <h2 class="section-title">@text($group['title'] ?? '')</h2>
        </div>
        <div class="reveal">
          <x-cms.faq-accordion :items="$group['items'] ?? []" :open-first="false" />
        </div>
      </div>
    @endforeach
  </div>
</section>
