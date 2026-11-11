{{--
    LazyUI <head> bootstrap. Place it early in <head>, before your own CSS.
    - applies the stored dark mode / accent / font before first paint (no flash)
    - emits the accent and font rules generated from config/lazyui.php
    - loads the compiled LazyUI JS (assets=false when the page loads it itself, e.g. Filament)
    CSS comes from your own Tailwind build: @import '.../vendor/lazyui/lazyui/resources/css/index.css'.
    Filament panels get this automatically through LazyUIPlugin.
--}}
@props([
    'assets' => true,
    'fonts' => true,
    'exceptFonts' => [],
    'tone' => null,
])

@php($appearance = \LazyUI\Support\Appearance::class)

<script data-lazyui @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>
    window.LazyUIConfig = @js($appearance::clientConfig());
    document.documentElement.dataset.lazyTone = @js($appearance::tone($tone));
    window.lazyReady = (callback) => document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', callback, { once: true })
        : callback();
    {!! \LazyUI\LazyUI::themeScript() !!}
</script>
<style data-lazyui @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>{!! $appearance::css() !!}</style>

@if ($fonts)
    @foreach ($appearance::fontUrls($exceptFonts) as $url)
        <link rel="stylesheet" href="{{ $url }}">
    @endforeach
@endif

@if ($assets)
    <script src="{{ \LazyUI\LazyUI::scriptUrl() }}" defer data-lazyui @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif></script>
@endif
