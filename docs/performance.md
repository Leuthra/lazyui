# Performance

Measured on the workbench panel (dashboard, products table, product edit form). "Off" is the same panel without
LazyUI (cookie `lazyui_bench=off`), with medians of alternating requests.

| | LazyUI cost |
|---|---|
| Server time per request | **~2 ms** (render hooks + generated CSS) |
| HTML per page | ~28 KB raw / **~5.7 KB gzip** (inline theme script, accent CSS, appearance sheet) |
| CSS `dist/filament.css` | 90 KB raw / **12.7 KB gzip**, cached by the browser |
| JS `dist/lazyui-filament.js` | 38 KB raw / **8.8 KB gzip**, cached by the browser |
| Scroll smoothness | **no extra dropped frames** (p95 frame time 16.7 ms with and without) |
| Layout shift (CLS) | 0 |

Front-end pages load `dist/lazyui.js` (75 KB / 21.7 KB gzip, includes tippy.js) and compile LazyUI's CSS in the app's
own Tailwind build, so there is no extra stylesheet.

## Decisions that keep it fast

- **No blur on large fixed areas.** `backdrop-filter` is recomputed on every scroll frame. The sidebar and slide-overs
  are opaque. Only the topbar uses a light blur (`--lz-glass-blur: 6px`, set it to `0px` to disable), and
  `prefers-reduced-transparency` turns it off.
- **Panel chrome is plain Blade.** The appearance sheet and gear button rendered on every panel page don't use
  nested components or twMerge (`resources/views/partials`).
- **twMerge only when needed.** `$attributes->twMerge()` skips TailwindMerge when the caller passes no `class`,
  which is the common case. TailwindMerge's first call per request costs several milliseconds.
- **Config-derived values are memoised** (`Appearance`), and the inline theme script is minified (`dist/theme.js`, 3 KB).
- **Separate bundles.** Panels don't load tippy.js (Filament has its own tooltips), and there is no SimpleBar
  (native thin scrollbars instead). The panel CSS doesn't include FilePond/tippy styles.
- **Precompiled CSS emits no Tailwind theme variables** (`theme(reference inline)`), so it adds rules only.

## Measuring again

`composer serve`, log in, then compare TTFB with and without LazyUI:

```bash
for i in $(seq 10); do for v in on off; do
  curl -s -o /dev/null -w "$v %{time_starttransfer}\n" -H "Cookie: <session cookies>; lazyui_bench=$v" http://127.0.0.1:8000/admin
done; done
```

In production, make sure OPcache is on and run `php artisan optimize` and `php artisan filament:optimize`.
Serve `public/css`, `public/js` and `public/vendor/lazyui` with long cache headers and gzip/brotli.
