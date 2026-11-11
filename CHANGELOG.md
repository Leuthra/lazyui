# Changelog

All notable changes to this package. Versions follow [SemVer](https://semver.org).

## [Unreleased]

## [2.0.0]

First release of the Filament 5 / Livewire 4 port of [ilsyaa/lazyui](https://github.com/ilsyaa/lazyui) 1.0.7.

### Added
- `LazyUIPlugin` for Filament panels: LazyUI palette, accent-driven primary colour, restyled panel
  components, appearance sheet next to the user menu. Options: `precompiled()`, `settingsButton()`, `palette()`.
- Precompiled `dist/lazyui.js` and `dist/filament.css`, served through FilamentAsset: panels without a custom
  theme need no setup at all. Front-end CSS is compiled by the app (`resources/css/index.css`), as is a custom
  panel theme (`resources/css/filament.css`), so every utility exists once per page.
- No npm dependencies for apps: own checkbox styles and animations replace `@tailwindcss/forms` and `tailwindcss-motion`.
- Accents and fonts come from `config/lazyui.php`; their CSS is generated at runtime.
- `lazy:install` registers the plugin on every panel provider (and imports LazyUI into custom panel themes and
  `app.css`); `lazy:publish` copies components for editing.
- `<x-lazy::head>`, `<x-lazy::layout>` (dashboard shell), `<x-lazy::settings-button>`, `lazy::demo` playground.
- Test suite (Testbench) and CI, including a check that `dist/` is up to date.

- LazyUI design system for Filament panels (`resources/css/partials/filament/`, `--lz-*` tokens) covering navigation,
  headers, forms, tables, overlays, notifications, tabs, wizard, widgets and auth pages. `tone`: `mono` (default) / `accent`.
- `php artisan about` shows whether LazyUI's assets are published; inline script/style carry the Vite CSP nonce.
- Workbench panel using every Filament feature (`composer workbench`, `composer serve`).
- `FilamentContractTest`, a Playwright visual regression suite (light/dark/mobile, overflow and JS-error checks),
  a weekly CI run against the newest Filament release and a lowest-dependencies job.
- Performance work: ~2 ms server overhead per panel request, no scroll jank (no blur on large fixed areas), slimmer
  panel bundle (`dist/lazyui-filament.js`), plain-Blade panel chrome, twMerge fast path, minified inline theme script.

### Changed (from ilsyaa/lazyui 1.0.7)
- Components are namespaced `x-lazy::` and served from the package.
- Dark mode uses the `dark` class and Filament's storage key, so panels and front end stay in sync.
- The sidebar store is now `$store.lazySidebar` (Filament owns `$store.sidebar`). On Filament pages,
  Filament's own `x-tooltip` is kept.
- `twMerge` is built in on top of `gehrisandro/tailwind-merge-php` (no Guzzle 7 lock, so it installs on Laravel 13).

### Fixed
- `form` crashed when toasts were disabled. It now sends `X-CSRF-TOKEN` and `Accept: application/json`.
- Toast content is HTML-escaped. Alpine expressions built from props use `@js` (quotes no longer break them).
- `nav.section :is-open="false"` produced invalid JavaScript. Stray `;` in `twMerge()` calls.
- TinyMCE initial content broke on backticks. FilePond/TinyMCE now also initialise after Livewire navigation.
- `dialog` / `sheet` survive Livewire morphs and `wire:navigate`.
- The mobile layout no longer overflows horizontally. The sidebar has a close button. Sheets never exceed the screen width.

### Removed
- TinyMCE "ai" plugin (Tiny commercial license) and the `route('ai')` hook.
- highlight.js per-language files (the core bundle remains). Hot-linked GitHub images (now local).
- SimpleBar (native thin scrollbars instead).
