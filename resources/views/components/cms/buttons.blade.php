@props(['buttons' => [], 'class' => ''])
@if(! empty($buttons))
  <div class="{{ $class }}">
    @foreach($buttons as $button)
      <x-cms.button :button="$button" />
    @endforeach
  </div>
@endif
