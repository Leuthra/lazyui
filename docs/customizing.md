# Customizing

## Accents

`config/lazyui.php` → `accents`. The order is the order in the settings sheet, and the first entry is the
fallback default. Values can be:

```php
'accents' => [
    'teal'  => Color::Teal,                 // a Filament palette
    'brand' => Color::hex('#e11d48'),       // generated from one colour
    'mint'  => '#10b981',                   // same, shorthand
    'exact' => [50 => 'oklch(...)', /* ... */ 950 => 'oklch(...)'],   // hand-picked
],
```

The accent becomes `--color-accent-*` (so `bg-accent-500` works), and in panels it is also Filament's `primary`.
The CSS is generated per request by `LazyUI\Support\Appearance::css()`, so nothing needs rebuilding.

To pin one accent and hide the picker, configure a single accent.

## Tone

`'tone' => 'mono'` (default) gives the LazyUI look: primary buttons, toggles, checkboxes and active wizard steps are
black in light mode and white in dark mode, and the accent marks active navigation, focus and highlights.
`'accent'` uses the accent for those solid surfaces too. Per panel: `LazyUIPlugin::make()->tone('accent')`.

## Design tokens

The panel look is driven by CSS variables in `resources/css/partials/filament/tokens.css`. Override any of them
in your panel theme (after the LazyUI import), with no rebuild of LazyUI needed:

```css
:root {
    --lz-radius-card: 1rem;      /* rounder cards */
    --lz-radius-control: 0.375rem;
    --lz-glass-blur: 0px;        /* solid topbar */
}
.dark { --lz-surface: var(--color-cat-750); }
```

## Fonts

```php
'fonts' => [
    'inter'   => ['label' => 'Inter', 'family' => "'Inter', sans-serif", 'url' => 'https://fonts.bunny.net/css?family=inter:400,500,600'],
    'jakarta' => ['label' => 'Jakarta', 'family' => "'Plus Jakarta Sans', sans-serif", 'url' => 'https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600'],
],
```

With one font, the picker is hidden. Filament panels already load Inter, so its `url` is skipped there.

## Defaults

`defaults.mode` (`auto|light|dark`), `defaults.accent`, `defaults.font`, or the `.env` keys
`LAZYUI_MODE`, `LAZYUI_ACCENT` and `LAZYUI_FONT`. Users keep their own choice in localStorage. "Reset settings" returns to these defaults.

## Plugin options

```php
LazyUIPlugin::make()
    ->precompiled(false)      // don't load dist/filament.css: the panel theme imports resources/css/filament.css
    ->settingsButton(false)   // no gear in the topbar (the sheet still exists: x-sheet:toggle="lazySettings")
    ->palette(false);         // keep the panel's colours
```

Colours set with `->colors([...])` *after* `->plugin(...)` override the plugin's palette.

## Changing a component

```bash
php artisan lazy:publish button dropdown     # or --all
```

This copies the component into `resources/views/vendor/lazy/components/`, and your copy wins over the package version.
Copies don't receive package updates, so publish only what you really change.

Prefer `class="..."` / attributes on the call site when you can, because twMerge resolves the conflicts.

## Restyling more of Filament

Give the panel a custom theme (`php artisan make:filament-theme`), import LazyUI into it and use
`->precompiled(false)`. Rules written *after* the import override LazyUI's. To change LazyUI's panel
rules for every app, edit `resources/css/partials/filament.css` in this package (see [architecture](architecture.md)).

## Using the tokens in app views

`lazy:install` adds this to `resources/css/app.css`:

```css
@import '../../vendor/lazyui/lazyui/resources/css/index.css';
```

Then `bg-cat-800`, `text-accent-500` and `dark:` work in your own Blade files. For the tokens only,
without the LazyUI component styles, import `resources/css/theme.css` instead.

## Known limitations

- Chart widgets (Chart.js) read the primary colour when they render, so after switching accent in the
  appearance sheet the charts follow on the next page load.
- The appearance sheet's settings live in the browser (localStorage), not on the user record. To sync them
  across devices, save `window.LazySettings.state` yourself (listen to the `lazy-appearance-change` event).
