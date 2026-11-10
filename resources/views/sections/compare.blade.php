@php
  $columns = $data['columns'] ?? [];
  $span = count($columns) + 1;
  $cell = function (?string $value): string {
      return match (strtolower(trim((string) $value))) {
          'yes' => '<span class="cmp-yes"><i class="fa-solid fa-check" aria-hidden="true"></i><span class="sr-only">Included</span></span>',
          'no' => '<span class="cmp-no"><i class="fa-solid fa-xmark" aria-hidden="true"></i><span class="sr-only">Not included</span></span>',
          default => (string) Cms::text($value),
      };
  };
@endphp
<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  <div class="compare-wrap reveal" tabindex="0" role="region" aria-label="{{ $data['caption'] ?? '' }}">
    <table class="compare-table">
      <caption class="sr-only">@text($data['caption'] ?? '')</caption>
      <thead>
        <tr>
          <th scope="col">@text($data['first_header'] ?? 'Features')</th>
          @foreach($columns as $column)
            <th scope="col"@if(! empty($column['featured'])) class="is-featured"@endif><span class="cmp-plan">@text($column['name'] ?? '')</span><span class="cmp-price">@text($column['price'] ?? '')<small>@text($column['period'] ?? '')</small></span></th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($data['rows'] ?? [] as $row)
          @if(! empty($row['group']))
            <tr class="cmp-group"><th scope="rowgroup" colspan="{{ $span }}">@text($row['label'] ?? '')</th></tr>
          @else
            <tr>
              <th scope="row">@text($row['label'] ?? '')</th>
              @foreach($columns as $index => $column)
                <td{!! ! empty($column['featured']) ? ' class="is-featured"' : '' !!}>{!! $cell($row['values'][$index] ?? '') !!}</td>
              @endforeach
            </tr>
          @endif
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th scope="row">@text($data['footer_label'] ?? 'Get started')</th>
          @foreach($columns as $column)
            <td{!! ! empty($column['featured']) ? ' class="is-featured"' : '' !!}><a class="btn {{ ! empty($column['featured']) ? 'btn-primary' : 'btn-outline' }}" href="{{ Cms::url($column['button_url'] ?? '#') }}">@text($column['button_label'] ?? 'Order')</a></td>
          @endforeach
        </tr>
      </tfoot>
    </table>
  </div>
  <x-cms.extras :data="$data" />
</x-cms.section>
