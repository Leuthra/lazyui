{{--
    LazyUI dashboard shell (front end).

    <x-lazy::layout title="Dashboard">
        <x-slot:menu> <x-lazy::nav.section label="Menu"> ... </x-lazy::nav.section> </x-slot:menu>
        <x-slot:actions> ...extra header buttons... </x-slot:actions>
        <x-slot:user> ...avatar dropdown... </x-slot:user>
        Page content
    </x-lazy::layout>
--}}
@props([
    'title' => null,
    'vite' => config('lazyui.vite', ['resources/css/app.css', 'resources/js/app.js']),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lazy">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ filled($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <x-lazy::head />
    @livewireStyles
    @if (! empty($vite))
        @vite($vite)
    @endif
    @stack('head')
</head>
<body class="lazy-body">
    <div class="lazy-layout">
        <x-lazy::layout.sidebar>
            @isset($brand)
                <x-slot:brand>{{ $brand }}</x-slot:brand>
            @endisset
            {{ $menu ?? '' }}
        </x-lazy::layout.sidebar>

        <main class="lazy-main">
            <x-lazy::layout.header>
                {{ $actions ?? '' }}
                @isset($user)
                    <x-slot:user>{{ $user }}</x-slot:user>
                @endisset
            </x-lazy::layout.header>

            {{ $slot }}
        </main>
    </div>

    <x-lazy::layout.settings />
    <x-lazy::toast />

    @livewireScripts
    @stack('body')
</body>
</html>
