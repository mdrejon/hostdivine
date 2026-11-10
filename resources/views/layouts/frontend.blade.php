@php
  // @section('x', $value) stores an escaped string; decode so {{ }} escapes exactly once.
  $yielded = fn (string $name) => trim(html_entity_decode($__env->yieldContent($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
  $siteName = Cms::setting('site_name', 'Host Divine™ Cloud Hosting');
  $metaTitle = $yielded('meta_title') ?: $siteName;
  $metaDescription = $yielded('meta_description') ?: Cms::setting('site_tagline', '');
  $metaKeywords = $yielded('meta_keywords');
  $ogImage = $yielded('og_image') ?: 'assets/img/header-home.webp';
  $preloadImage = $yielded('preload_image');
  $version = fn (string $path) => asset($path).'?v='.(@filemtime(public_path($path)) ?: '1');
@endphp
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $metaTitle }}</title>
    @if($metaDescription)
      <meta content="{{ $metaDescription }}" name="description">
    @endif
    @if($metaKeywords)
      <meta content="{{ $metaKeywords }}" name="keywords">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ Cms::asset($ogImage) }}">
    <meta name="twitter:card" content="summary_large_image">

    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180">
    <link href="{{ Cms::asset(Cms::setting('site_favicon', 'assets/img/favicon.svg')) }}" rel="icon" type="image/svg+xml">
    <link href="{{ asset('assets/img/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png">
    <link href="{{ asset('assets/img/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png">
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">

    {{-- Self-hosted fonts and icons: no third-party requests, no render-blocking @import chain. --}}
    <link rel="preload" href="{{ asset('assets/fonts/inter-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/manrope-latin-800-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    @if($preloadImage)
      <link href="{{ Cms::asset($preloadImage) }}" rel="preload" as="image" fetchpriority="high">
    @endif
    <link href="{{ $version('assets/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ $version('assets/vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ $version('assets/css/style.css') }}" rel="stylesheet">
    <script>document.documentElement.classList.add('js');</script>

    @stack('schema')

    @if($analyticsId = Cms::setting('google_analytics_id'))
      <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsId }}"></script>
      <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $analyticsId }}');</script>
    @endif
    {!! Cms::setting('header_scripts', '') !!}
  </head>
  <body>
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.header')

    <main id="main"@hasSection('hero_background') data-hero="@yield('hero_background')"@endif>
      @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.quick-dock')

    {!! Cms::setting('footer_scripts', '') !!}
    <script defer src="{{ $version('assets/js/main.js') }}"></script>
  </body>
</html>
