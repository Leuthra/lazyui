<?php

use Filament\Support\Colors\Color;

return [

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    | Used until the user picks something in the appearance sheet.
    | mode: auto | light | dark     aside (front-end sidebar): show | hide
    | accent / font: a key from the lists below.
    */
    'defaults' => [
        'mode' => env('LAZYUI_MODE', 'auto'),
        'accent' => env('LAZYUI_ACCENT', 'indigo'),
        'font' => env('LAZYUI_FONT', 'inter'),
        'aside' => 'show',
    ],

    /*
    |--------------------------------------------------------------------------
    | Accents
    |--------------------------------------------------------------------------
    | Shown in the appearance sheet, in this order. The chosen accent also becomes the
    | Filament "primary" colour. A value can be a Filament palette (Color::Teal), a
    | generated one (Color::hex('#0ea5e9')) or a single colour string ('#0ea5e9').
    | Adding one here is enough: the CSS is generated, no rebuild needed.
    */
    'accents' => [
        'emerald' => Color::Emerald,
        'indigo' => Color::Indigo,
        'blue' => Color::Blue,
        'pink' => Color::Pink,
        'orange' => Color::Orange,
        'purple' => Color::Purple,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fonts
    |--------------------------------------------------------------------------
    | family: CSS font-family stack. url (optional): stylesheet that loads the font.
    */
    'fonts' => [
        'inter' => [
            'label' => 'Inter',
            'family' => "'Inter Variable', 'Inter', ui-sans-serif, system-ui, sans-serif",
            'url' => 'https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap',
        ],
        'sans' => [
            'label' => 'Sans',
            'family' => "ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji'",
        ],
        'monospace' => [
            'label' => 'Monospace',
            'family' => "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', monospace",
        ],
        'serif' => [
            'label' => 'Serif',
            'family' => "ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tone
    |--------------------------------------------------------------------------
    | mono:   primary buttons, toggles, checkboxes are black / white (LazyUI's default look);
    |         the accent marks active navigation, focus and highlights.
    | accent: those solid surfaces use the accent colour instead.
    | Per panel: LazyUIPlugin::make()->tone('accent').
    */
    'tone' => env('LAZYUI_TONE', 'mono'),

    // localStorage key for accent / font / sidebar. Dark mode always uses Filament's own key.
    'storage_key' => 'lazy-settings',

    // Ctrl + D toggles dark mode.
    'dark_mode_shortcut' => true,

    // Vite entries loaded by <x-lazy::layout> (your app's own CSS/JS). [] to load none.
    'vite' => ['resources/css/app.css', 'resources/js/app.js'],

];
