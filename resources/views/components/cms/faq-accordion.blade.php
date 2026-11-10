@props(['items' => [], 'openFirst' => true])
<div class="faq-accordion">
  @foreach($items as $item)
    <details class="faq-item"@if($openFirst && $loop->first) open @endif>
      <summary class="faq-summary"><span>@text($item['question'] ?? '')</span><i class="fa-solid fa-chevron-down faq-chevron" aria-hidden="true"></i></summary>
      <div class="faq-content">{!! $item['answer'] ?? '' !!}</div>
    </details>
  @endforeach
</div>
