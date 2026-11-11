# Deployment

LazyUI adds nothing to your build pipeline beyond what Filament already needs.

## Checklist

```bash
composer install --no-dev --optimize-autoloader
php artisan filament:assets                          # LazyUI's dist/ CSS + JS (Filament runs this on composer update too)
php artisan vendor:publish --tag=laravel-assets      # LazyUI images (the default composer post-update script does this)
npm ci && npm run build                              # your app.css / panel theme (LazyUI's CSS compiles in when imported)
php artisan optimize
php artisan filament:optimize
php artisan about --only=lazyui                      # Assets: PUBLISHED
```

`php artisan about` reports **MISSING** when the compiled assets or images aren't in `public/`. That is the usual
cause of an unstyled panel or `$store.sheet is undefined` errors in the browser console.

Commit or build `public/css`, `public/js` and `public/vendor/lazyui` consistently with how you handle
Filament's own published assets (most teams publish them during deploy and keep them out of git).

## Updating

```bash
composer update lazyui/lazyui     # filament:upgrade republishes the assets
```

Read the package `CHANGELOG.md` first. Patch and minor releases don't change component props, config keys or the Alpine API.

## Server

- **OPcache on.** LazyUI's server cost (~2 ms per panel request) assumes compiled Blade views are cached.
- **Long-lived caching + gzip/brotli** for `/css`, `/js`, `/vendor/lazyui`. URLs carry a `?v=` version, so they are safe to cache.
- **Content Security Policy.** LazyUI renders one inline `<script>` and one inline `<style>` in `<head>` (the appearance
  bootstrap, which avoids a flash of the wrong theme). They carry `Vite::cspNonce()` automatically, so use
  Laravel's Vite nonce (`Vite::useCspNonce()`), as Filament does. Fonts load from `fonts.bunny.net` on front-end pages
  unless you remove the `url` from `config('lazyui.fonts')`.

## Environment switches

```
LAZYUI_MODE=auto        # default dark mode: auto | light | dark
LAZYUI_ACCENT=indigo    # default accent (a key of config('lazyui.accents'))
LAZYUI_FONT=inter
LAZYUI_TONE=mono        # mono | accent
```

These are only defaults. Users' choices in the appearance sheet are stored in their browser.
