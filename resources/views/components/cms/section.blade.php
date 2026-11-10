@props(['data' => [], 'class' => '', 'head' => true])
@php
  $background = trim((string) ($data['background'] ?? ''));
  $sectionClass = trim('section '.$class.' '.$background);
@endphp
<section class="{{ $sectionClass }}"@if(! empty($data['anchor'])) id="{{ $data['anchor'] }}"@endif>
  <div class="container">
    @if($head)
      <x-cms.head :data="$data" />
    @endif
    {{ $slot }}
  </div>
</section>
