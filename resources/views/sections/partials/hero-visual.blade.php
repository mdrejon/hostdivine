{{-- Decorative mock-ups for the homepage hero slider. $slide['visual'] picks one; chips are editable. --}}
@php($visual = $slide['visual'] ?? 'photo')
<div class="hero-visual visual-{{ $visual }}"@if($visual !== 'photo') aria-hidden="true"@endif>
  @if($visual === 'photo')
    <div class="photo-frame">
      <img src="{{ Cms::asset($slide['image'] ?? 'assets/img/hero-hosting.svg') }}" alt="{{ $slide['image_alt'] ?? '' }}" width="800" height="640" decoding="async"@if($first) fetchpriority="high"@else loading="lazy"@endif>
    </div>
  @elseif($visual === 'dashboard')
    <div class="mock-window">
      <div class="mock-bar"><span class="mock-dots"><i></i><i></i><i></i></span><span class="mock-url"><i class="fa-solid fa-lock"></i> whm.yourbrand.com</span></div>
      <div class="mock-body">
        <div class="mock-kpis">
          <div class="kpi"><small>cPanel accounts</small><strong>148</strong><span class="kpi-bar"><i style="--w: 74%"></i></span></div>
          <div class="kpi"><small>Disk used</small><strong>112 GB</strong><span class="kpi-bar"><i style="--w: 58%"></i></span></div>
          <div class="kpi"><small>Bandwidth</small><strong>Unmetered</strong><span class="kpi-bar"><i style="--w: 36%"></i></span></div>
        </div>
        <div class="mock-chart"><span style="--h: 38%"></span><span style="--h: 52%"></span><span style="--h: 44%"></span><span style="--h: 66%"></span><span style="--h: 58%"></span><span style="--h: 74%"></span><span style="--h: 62%"></span><span style="--h: 82%"></span><span style="--h: 70%"></span><span style="--h: 90%"></span><span style="--h: 78%"></span><span style="--h: 96%"></span></div>
        <ul class="mock-list">
          <li><span class="mock-avatar">AC</span><span>acme-store.com</span><em>Business</em><b class="pill-ok">Active</b></li>
          <li><span class="mock-avatar is-blue">NB</span><span>nordic-blog.net</span><em>Starter</em><b class="pill-ok">Active</b></li>
          <li><span class="mock-avatar is-green">LP</span><span>launchpad.io</span><em>Pro</em><b class="pill-new">New</b></li>
        </ul>
      </div>
    </div>
  @elseif($visual === 'domain')
    <div class="mock-window">
      <div class="mock-bar"><span class="mock-dots"><i></i><i></i><i></i></span><span class="mock-url is-secure"><i class="fa-solid fa-lock"></i> https://yourbrand.com</span></div>
      <div class="mock-body">
        <div class="mock-search"><i class="fa-solid fa-magnifying-glass"></i><span>yourbrand</span><b>Search</b></div>
        <ul class="domain-results">
          <li><span><strong>yourbrand</strong>.com</span><em>Available</em><b>$9.56</b><i class="fa-solid fa-cart-plus"></i></li>
          <li><span><strong>yourbrand</strong>.co</span><em>Available</em><b>$4.56</b><i class="fa-solid fa-cart-plus"></i></li>
          <li><span><strong>yourbrand</strong>.net</span><em>Available</em><b>$8.99</b><i class="fa-solid fa-cart-plus"></i></li>
          <li class="is-taken"><span><strong>yourbrand</strong>.org</span><em>Taken</em><b>—</b><i class="fa-solid fa-ban"></i></li>
        </ul>
      </div>
    </div>
  @else
    <div class="rack">
      <div class="rack-head"><span>HS-NODE-01</span><span class="rack-status"><i></i>Online</span></div>
      @for($unit = 0; $unit < 6; $unit++)
        <div class="rack-unit"><span class="vents"></span><span class="leds"><i></i><i></i><i></i></span></div>
      @endfor
    </div>
    <div class="terminal">
      <div class="terminal-bar"><span class="mock-dots"><i></i><i></i><i></i></span><span>root@vps-01</span></div>
                  <pre><span class="t-prompt">$</span> ssh root@vps-01
<span class="t-ok">✔</span> Connected · KVM · NVMe
<span class="t-prompt">$</span> uptime
 up 214 days, load 0.12
<span class="t-prompt">$</span> <span class="t-cursor"></span></pre>
    </div>
  @endif
  @foreach($slide['chips'] ?? [] as $chip)
    <div class="float-chip chip-{{ $chip['position'] ?? 'bl' }}">
      <span class="chip-icon is-{{ $chip['color'] ?? 'blue' }}"><i class="{{ $chip['icon'] ?? 'fa-solid fa-bolt' }}" aria-hidden="true"></i></span>
      <div><strong>@text($chip['title'] ?? '')</strong><small>@text($chip['text'] ?? '')</small></div>
    </div>
  @endforeach
</div>
