# LazyUI

The LazyUI look for **Filament 5** panels, plus a matching Blade + Alpine component kit for the rest
of your Laravel app. A single settings sheet (dark mode, accent, font) drives the front end and every panel.

Laravel 12/13 · Livewire 4 · Filament 5 · Tailwind CSS 4 · PHP 8.3+

This is a fork of [ilsyaa/lazyui](https://github.com/ilsyaa/lazyui) by **Ilsyaa** (MIT). The original targets
Livewire 3; this fork brings it to Livewire 4 / Filament 5, keeps the same look and component API, and adds the
Filament panel design. Version 2.0.0 continues from the original's 1.0.7. See [CHANGELOG](CHANGELOG.md).

## Install in any Filament app

Add the repository once (until the package is on Packagist), then require it:

```bash
composer config repositories.lazyui vcs https://github.com/Leuthra/lazyui
composer require lazyui/lazyui:^2.0
php artisan lazy:install
```

`lazy:install`:
- adds `->plugin(LazyUIPlugin::make())` to every panel provider in `app/Providers/Filament`
- adds `@import '.../vendor/lazyui/lazyui/resources/css/index.css'` to `resources/css/app.css` (front-end pages)
- publishes the assets (`filament:assets` publishes them again on every `composer update`)

No npm packages are needed. There are two ways to style a panel:

| Panel | What LazyUI does |
|---|---|
| **No custom theme** (Filament default) | loads the precompiled `dist/filament.css`. Zero config. |
| **Custom theme** (`->viteTheme(...)`) | the theme imports `resources/css/filament.css` and the plugin uses `->precompiled(false)`, so LazyUI is compiled together with your theme. `lazy:install` detects this and sets up both. |

Manual setup:

```php
use LazyUI\Filament\LazyUIPlugin;

$panel->plugin(LazyUIPlugin::make());                      // no custom theme
$panel->plugin(LazyUIPlugin::make()->precompiled(false));  // custom theme, plus in theme.css:
// @import '../../../../vendor/lazyui/lazyui/resources/css/filament.css';
```

## What you get

| | |
|---|---|
| **Panel design** | not a recolour: LazyUI's own design for every Filament area (navigation, page headers, forms and all field types, tables, filters, modals, slide-overs, dropdowns, notifications, tabs as segmented controls, wizard, widgets, auth pages), with mono (black/white) or accent tone |
| **Appearance sheet** | dark / light / auto, accents and fonts from `config/lazyui.php`, stored per browser and shared between the front end and panels |
| **Components** | `<x-lazy::button>`, `dialog`, `sheet`, `dropdown`, `select`, `autocomplete`, `tabs`, `accordion`, `toast`... see [docs/components.md](docs/components.md) |
| **Dashboard shell** | `<x-lazy::layout>` (sidebar, header, settings) for non-Filament pages |
| **JS helpers** | `toast.success('Saved')`, `zalert({...})`, `$this->dispatch('toast', ...)` from Livewire |

## Configure

```bash
php artisan vendor:publish --tag=lazyui-config
```

```php
// config/lazyui.php
'defaults' => ['mode' => 'auto', 'accent' => 'indigo', 'font' => 'inter'],
'accents' => [
    'indigo' => Color::Indigo,
    'brand'  => Color::hex('#e11d48'),   // added = done. No CSS change, no rebuild.
],
```

Plugin options (per panel):

```php
LazyUIPlugin::make()
    ->precompiled(false)      // the panel's own Vite theme imports LazyUI's CSS
    ->settingsButton(false)   // hide the gear next to the user menu
    ->palette(false);         // keep the panel's own ->colors()
```

More in [docs/customizing.md](docs/customizing.md).

## Front-end pages

```blade
<x-lazy::layout title="Dashboard">
    <x-slot:menu>
        <x-lazy::nav.section label="Menu">
            <x-lazy::nav.item href="/" :is-active="true">
                <x-slot:icon><svg>...</svg></x-slot:icon>
                <x-slot:title>Home</x-slot:title>
            </x-lazy::nav.item>
        </x-lazy::nav.section>
    </x-slot:menu>

    <div class="lazy-container-sm">...</div>
</x-lazy::layout>
```

Front-end CSS is compiled by your app's Tailwind build (`@import '.../resources/css/index.css'` in `app.css`),
so `bg-cat-*`, `text-accent-*` and `dark:` also work in your own views. The JS is precompiled and loaded by `<x-lazy::head />`.

To use your own layout, put `<x-lazy::head />` in `<head>`, then add `<x-lazy::layout.settings />`,
`<x-lazy::toast />` and `@stack('body')` before `</body>`.

Render `lazy::demo` on any route to see every component: `Route::view('/lazyui', 'lazy::demo')`.
To see the panel design on every Filament feature, run the workbench from this repo (`composer workbench && composer serve`).

## Docs

- [Components](docs/components.md): every component, its props and examples
- [Customizing](docs/customizing.md): accents, fonts, publishing components, plugin options
- [Architecture](docs/architecture.md): how the assets, theme sync, design tokens and Filament integration work
- [Deployment](docs/deployment.md): checklist, `php artisan about`, CSP, caching
- [When Filament updates](docs/filament-updates.md): what's checked automatically and the playbook
- [Performance](docs/performance.md): measured cost (~2 ms server, ~21 KB gzip cached assets) and the decisions behind it
- [Contributing](CONTRIBUTING.md): local setup, tests, building `dist/`, releasing
- [Changelog](CHANGELOG.md)

## License

MIT. Bundled third-party builds in `public/vendor` keep their own licenses (see [LICENSE.md](LICENSE.md)).
