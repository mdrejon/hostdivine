{{-- Optional blocks that can follow a section body: logo strip, note and centred buttons. --}}
@props(['data' => []])
@if(! empty($data['logos']))
  <ul class="tech-strip reveal" aria-label="{{ $data['logos_label'] ?? 'Included software' }}">
    @foreach($data['logos'] as $logo)
      <li><img src="{{ Cms::asset($logo['image'] ?? '') }}" alt="{{ $logo['alt'] ?? '' }}" width="360" height="96" loading="lazy" decoding="async"></li>
    @endforeach
  </ul>
@endif
@if(! empty($data['note']))
  <p class="plans-note{{ ! empty($data['note_reveal']) ? ' reveal' : '' }}"><i class="{{ $data['note_icon'] ?? 'fa-solid fa-circle-info' }}" aria-hidden="true"></i>{!! $data['note'] !!}</p>
@endif
<x-cms.buttons :buttons="$data['actions'] ?? []" class="center-actions" />
