# Architecture

How the pieces fit, and where to go when something needs to change.

## File map

```
config/lazyui.php                 defaults, tone, accents, fonts (the only thing apps usually touch)
src/
  LazyUIServiceProvider.php       views (lazy::), FilamentAsset registration, publish tags, twMerge macro
  LazyUI.php                      paths, asset URLs, twMerge()
  Support/Appearance.php          config → accent/font CSS, client config, defaults validation
  Filament/LazyUIPlugin.php       panel palette + render hooks
  Commands/                       lazy:install, lazy:publish
resources/
  css/index.css                   front-end entry, imported by the app's app.css
  css/filament.css                panel entry, imported by a custom panel theme or built into dist/
  css/theme.css                   tokens only: cat, accent fallback, dark variant
  css/partials/filament/          the panel design, one file per area:
    tokens.css                      every design knob (surface, line, text, radius, shadow, glass, tone)
    layout.css navigation.css buttons.css forms.css surfaces.css tables.css overlays.css widgets.css
  css/partials/base.css           checkbox + animations (replaces @tailwindcss/forms and tailwindcss-motion)
  css/partials/vendor/            tippy.js + FilePond styles (front end only)
  css/partials/{utils,layout}.css
  css/entries/filament.css        build entry → dist/filament.css
  js/theme.js                     appearance store → dist/theme.js (minified, inlined in <head>)
  js/core.js + plugins/           Alpine plugins, toast, alert
  js/index.js / js/filament.js    bundles → dist/lazyui.js (front end, + tooltips) / dist/lazyui-filament.js (panels)
  views/components/               the components (lazy::*)
  views/filament/                 render-hook partials used by the plugin
  views/partials/                 appearance sheet + gear as plain Blade (rendered on every panel page)
  views/demo.blade.php            playground (also rendered by the tests)
dist/                             compiled CSS/JS, committed, built with `npm run build`
public/                           images + optional FilePond/TinyMCE/ApexCharts/highlight.js (vendor:publish)
tests/Feature/                    PHPUnit (Testbench): components, appearance, plugin, commands, Filament contract
tests/visual/                     Playwright visual regression against the workbench
workbench/                        demo Filament panel using every Filament feature (composer workbench / serve)
```

## Request flow in a Filament panel

`LazyUIPlugin::register()` hooks into the panel:

| Hook | Output |
|---|---|
| `HEAD_START` | `<x-lazy::head :assets="false">`: `window.LazyUIConfig`, `data-lazy-tone`, `theme.js` inline, generated accent/font `<style>` |
| `STYLES_AFTER` | `dist/filament.css`, placed after Filament's theme so its rules win the cascade (skipped with `->precompiled(false)`) |
| `SCRIPTS_BEFORE` | `dist/lazyui-filament.js`, which registers the Alpine plugins on `alpine:init`, before Livewire boots Alpine |
| `USER_MENU_BEFORE` | settings gear |
| `BODY_END` | appearance sheet (`partials/settings`), `@stack('body')`. `toast()` creates its container on first use |

It also registers `->colors(['gray' => cat, 'primary' => default accent])`. At runtime,
`html.lazy[theme-accent] { --primary-*: var(--color-accent-*) }` (in `partials/filament.css`) makes Filament's
primary follow the accent the user picks.

## Front-end pages

`<x-lazy::head />` outputs the same bootstrap plus `dist/lazyui.js`. The CSS is **not** precompiled for the front end:
the app's `app.css` imports `resources/css/index.css`, which `@source`s the package views, so the app's own Tailwind
build compiles LazyUI together with the app views. `<x-lazy::layout>` wraps everything into a full page with
`@livewireStyles/Scripts` (Alpine comes from Livewire).

## The one rule for CSS: every utility is compiled once per page

Two Tailwind builds on one page fight. If both contain `.bg-white` but only one contains `.dark:bg-cat-800`,
whichever file loads last silently wins and dark mode breaks. That's why:

- **Front end:** one build (the app's) includes LazyUI's source.
- **Panel with a custom theme:** one build (the theme) includes `resources/css/filament.css`, and the plugin uses `->precompiled(false)`.
- **Panel without a custom theme:** Filament's CSS styles its markup through `.fi-*` classes, not raw utilities,
  so the precompiled `dist/filament.css` can be added safely. It is built with `theme(reference inline)`: utilities get
  literal values and no Tailwind `--color-*`/`--font-*`/`--spacing` variables are emitted, so Filament's variables
  stay untouched. Only LazyUI's own tokens (`--color-cat-*`, `--color-accent-*`) are defined, and there is no preflight.

The CSS doesn't depend on `@tailwindcss/forms` or `tailwindcss-motion` (see `partials/base.css`), so apps need no npm packages.

## Dark mode / appearance sync

- Mode is stored under Filament's key `filament:theme:mode` (`light|dark|system`) and applied as the `dark` class.
  `theme.js` dispatches `theme-changed` so Filament's `$store.theme` follows, and it listens to the same event,
  so Filament's own theme switcher updates LazyUI.
- Accent, font and sidebar are stored under `lazy-settings` (configurable) as attributes on `<html class="lazy">`.
- The settings sheet binds to the reactive Alpine store `$appearance` (`Alpine.store('lazy-settings')`).

## Name collisions with Filament (keep it this way)

Filament owns `$store.sidebar`, `$store.theme` and the `x-tooltip` directive. LazyUI uses `$store.lazySidebar`
and skips its tooltip directive on `<html class="fi">`. Check new Alpine stores/directives against Filament:

```bash
grep -rhoE "Alpine\.(store|directive|magic)\(['\"][a-zA-Z-]+" vendor/filament --include=*.js | sort -u
```

## The panel design system

`partials/filament/tokens.css` defines `--lz-*` variables (canvas, surface, line, text, ring, radius, shadows,
glass, solid). Every other file uses only those tokens, so a design change usually means one variable. The **tone**
(`mono` / `accent`) only swaps `--lz-solid` and `--lz-on-solid`, which primary buttons, toggles, checkboxes, radios,
wizard steps and pagination read.

Filament sets button colours through utility classes (`.fi-bg-color-600` → `--bg`). Rules that override those
variables therefore live in `@layer utilities` (see the end of `buttons.css`). Everything else is in `@layer components`.

## When Filament releases a new version

See [filament-updates.md](filament-updates.md). The contract test, visual regression suite and weekly CI run cover it.
