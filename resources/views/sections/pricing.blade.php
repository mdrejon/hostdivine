<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="pricing-grid pricing-grid-{{ $data['columns'] ?? 3 }} reveal">
    @foreach($data['plans'] ?? [] as $plan)
      <x-cms.plan-card :plan="$plan" />
    @endforeach
  </div>
  @if(! empty($data['table_rows']))
    @include('sections.partials.static-table', ['table' => $data])
  @endif
  <x-cms.extras :data="$data" />
</x-cms.section>
