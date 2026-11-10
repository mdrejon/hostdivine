@php($tabs = $data['tabs'] ?? [])
<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="tab-switch reveal" role="tablist" aria-label="{{ $data['tabs_label'] ?? 'Plan type' }}" data-tabs>
    @foreach($tabs as $tab)
      <button type="button" class="tab-btn{{ $loop->first ? ' is-active' : '' }}" role="tab" id="tab-{{ $tab['id'] ?? $loop->index }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-controls="panel-{{ $tab['id'] ?? $loop->index }}"@unless($loop->first) tabindex="-1"@endunless>@if(! empty($tab['icon']))<i class="{{ $tab['icon'] }}" aria-hidden="true"></i>@endif{{ Cms::text($tab['label'] ?? '') }}@if(! empty($tab['suffix']))<span class="tab-suffix"> @text($tab['suffix'])</span>@endif</button>
    @endforeach
  </div>
  @foreach($tabs as $tab)
    <div class="tab-panel{{ $loop->first ? ' is-active' : '' }}" role="tabpanel" id="panel-{{ $tab['id'] ?? $loop->index }}" aria-labelledby="tab-{{ $tab['id'] ?? $loop->index }}"@unless($loop->first) hidden @endunless>
      @if(($data['layout'] ?? 'grid') === 'slider')
        <div class="card-slider per-4" data-slider>
          <div class="slider-track" data-slider-track tabindex="0" aria-label="{{ $tab['slider_label'] ?? '' }}">
            @foreach($tab['plans'] ?? [] as $plan)
              <x-cms.plan-card :plan="$plan" />
            @endforeach
          </div>
          @include('sections.partials.slider-nav', ['navLabel' => $tab['nav_label'] ?? 'plans'])
        </div>
      @else
        <div class="pricing-grid pricing-grid-{{ $data['columns'] ?? 4 }} reveal">
          @foreach($tab['plans'] ?? [] as $plan)
            <x-cms.plan-card :plan="$plan" />
          @endforeach
        </div>
      @endif
    </div>
  @endforeach
  <x-cms.extras :data="$data" />
</x-cms.section>
