@php
  $mode = $data['mode'] ?? 'popular';
  $tlds = match ($mode) {
      'popular' => \App\Models\DomainTld::query()->inList('popular')->get(),
      'renew' => \App\Models\DomainTld::query()->inList('renew')->get(),
      default => \App\Models\DomainTld::query()->active()->get(),
  };
  $limit = (int) ($data['limit'] ?? 0);
  $caption = $data['caption'] ?? 'Domain prices in BDT';
  $actionUrl = Cms::url($data['action_url'] ?? 'https://robot.hostdivine.com/cart.php?a=add&domain=register');
  $money = fn ($value) => $value === null ? '<span class="tld-na">—</span>' : '<span class="tk">৳</span>'.Cms::price($value);
@endphp
<x-cms.section :data="$data" :class="$data['css_class'] ?? ''">
  @if($mode === 'all')
    <div class="tld-tools reveal">
      <label class="tld-search" for="tld-filter"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Search extensions</span><input id="tld-filter" type="search" placeholder="{{ $data['search_placeholder'] ?? 'Search extensions, e.g. .shop' }}" autocomplete="off" data-tld-filter></label>
      <p class="tld-count" data-tld-count aria-live="polite">{{ $tlds->count() }} extensions</p>
    </div>
  @endif
  <div class="tld-table-container reveal" tabindex="0" role="region" aria-label="{{ $caption }}">
    <table class="tld-table" data-tld-table @if($mode === 'all' && $limit) data-tld-limit="{{ $limit }}"@endif>
      <caption class="sr-only">{{ $caption }}</caption>
      <thead>
        <tr>
          <th scope="col">Extension</th>
          @if($mode === 'popular')
            <th scope="col">Register 1 yr</th><th scope="col">Register 2 yrs</th><th scope="col">Transfer</th>
          @else
            <th scope="col">1 Year</th><th scope="col">2 Years</th><th scope="col">3 Years</th><th scope="col">5 Years</th><th scope="col">10 Years</th>
          @endif
          <th scope="col"><span class="sr-only">Action</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach($tlds as $tld)
          <tr data-tld="{{ $tld->tld }}">
            <th scope="row"><span class="tld-badge">{{ $tld->tld }}</span></th>
            @if($mode === 'popular')
              <td>{!! $money($tld->price_1yr) !!}</td><td>{!! $money($tld->price_2yr) !!}</td><td>{!! $money($tld->transfer_price ?? $tld->price_1yr) !!}</td>
            @elseif($mode === 'renew')
              @foreach(array_keys(\App\Models\DomainTld::TERMS) as $years)
                <td>{!! $money($tld->renewalFor($years)) !!}</td>
              @endforeach
            @else
              @foreach(\App\Models\DomainTld::TERMS as $column)
                <td>{!! $money($tld->{$column}) !!}</td>
              @endforeach
            @endif
            <td><a class="tld-action" href="{{ $actionUrl }}">{{ $data['action_label'] ?? 'Register' }}</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <p class="tld-empty" data-tld-empty hidden>No extensions match your search.</p>
  </div>
  @if($mode === 'all' && $limit)
    <div class="tld-more-wrap"><button type="button" class="btn btn-outline" data-tld-more hidden>Show all {{ $tlds->count() }} extensions <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button></div>
  @endif
  <x-cms.extras :data="$data" />
</x-cms.section>
