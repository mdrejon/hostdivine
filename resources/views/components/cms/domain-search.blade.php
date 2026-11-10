{{-- Shared domain search card. Chips come from Domain Pricing (featured TLDs); the rest from Site Layout → Domain Search. --}}
@props(['mode' => 'register', 'prefix' => 'ds'])
@php
  $widget = Cms::layout('widget_domain_search');
  $chips = \App\Models\DomainTld::query()->inList('featured')->get();
  $extensions = $widget['extensions'] ?? ['.com'];
  $isTransfer = $mode === 'transfer';
@endphp
<div class="ds-card" data-domain-search>
  <div class="ds-modes" role="radiogroup" aria-label="Choose an action">
    <button type="button" class="ds-mode{{ $isTransfer ? '' : ' is-active' }}" role="radio" aria-checked="{{ $isTransfer ? 'false' : 'true' }}" data-ds-mode="register"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Register</button>
    <button type="button" class="ds-mode{{ $isTransfer ? ' is-active' : '' }}" role="radio" aria-checked="{{ $isTransfer ? 'true' : 'false' }}" data-ds-mode="transfer"><i class="fa-solid fa-right-left" aria-hidden="true"></i>Transfer</button>
  </div>
  <form class="ds-form" action="{{ Cms::setting('robot_cart_url', 'https://robot.hostdivine.com/cart.php') }}" method="get" data-ds-form>
    <input type="hidden" name="a" value="add">
    <input type="hidden" name="domain" value="{{ $isTransfer ? 'transfer' : 'register' }}" data-ds-action>
    <div class="ds-bar">
      <span class="ds-bar-icon" aria-hidden="true"><i class="fa-solid fa-globe" aria-hidden="true"></i></span>
      <label class="sr-only" for="{{ $prefix }}-ds-input">Domain name</label>
      <input class="ds-input" id="{{ $prefix }}-ds-input" name="query" type="text" inputmode="url" placeholder="{{ $isTransfer ? ($widget['transfer_placeholder'] ?? 'Enter the domain you want to transfer') : ($widget['register_placeholder'] ?? 'Find your perfect domain name…') }}" autocomplete="off" autocapitalize="off" spellcheck="false" required data-ds-input>
      <div class="ds-select">
        <label class="sr-only" for="{{ $prefix }}-ds-tld">Extension</label>
        <select id="{{ $prefix }}-ds-tld" data-ds-tld>
          @foreach($extensions as $extension)
            <option value="{{ $extension }}">{{ $extension }}</option>
          @endforeach
        </select>
        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
      </div>
      <button type="submit" class="ds-submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span data-ds-label>{{ $isTransfer ? 'Transfer' : 'Search' }}</span></button>
    </div>
  </form>
  <div class="ds-preview" data-ds-preview hidden>
    <p class="ds-preview-title">Popular extensions for <strong data-ds-name></strong></p>
    <div class="ds-preview-list" data-ds-preview-list></div>
  </div>
  <div class="ds-tlds" role="list" aria-label="Popular extensions and yearly prices">
    @foreach($chips as $chip)
      <button type="button" class="ds-tld{{ $loop->first ? ' is-active' : '' }}" role="listitem" data-tld="{{ $chip->tld }}" data-price="{{ Cms::wholePrice($chip->price_1yr) }}">@if($chip->badge)<em>{{ $chip->badge }}</em>@endif<span class="ds-tld-name">{{ $chip->tld }}</span><span class="ds-tld-price"><span class="tk">৳</span>{{ Cms::wholePrice($chip->price_1yr) }}<small>/yr</small></span></button>
    @endforeach
  </div>
  <div class="ds-foot">
    <ul class="ds-perks">
      @foreach($widget['perks'] ?? [] as $perk)
        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>@text($perk)</li>
      @endforeach
    </ul>
    <div class="ds-links">
      @foreach($widget['links'] ?? [] as $link)
        <a href="{{ Cms::url($link['url'] ?? '#') }}">@text($link['label'] ?? '') <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
      @endforeach
    </div>
  </div>
</div>
