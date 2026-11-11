# Contributing

## Setup

```bash
composer install
npm install
```

Work on the package inside a real app through a Composer path repository (that's how `laravel-template` uses it):

```json
"repositories": [{ "type": "path", "url": "../lazyui", "options": { "symlink": true } }]
```

```bash
composer require lazyui/lazyui:@dev
```

Edits to PHP and Blade show up immediately in the app. CSS imported from source (`index.css`, `filament.css`)
is rebuilt by the app's `npm run dev`. After JS changes, or to update `dist/filament.css`, run `npm run build`
here and `php artisan filament:assets` in the app.

## Workbench

A full Filament panel using every Filament feature, for developing the design and for the visual tests:

```bash
composer workbench     # migrate + seed + publish assets (SQLite; copy testbench.yaml.dist to testbench.yaml for MySQL)
composer serve         # http://127.0.0.1:8000/admin   admin@lazyui.test / password, / = component playground
```

## Checks

```bash
composer test        # PHPUnit (Testbench, real Filament panel)
composer lint        # Pint, check only
composer format      # Pint, fix
npm run build        # rebuild dist/, which must be committed (CI fails when dist/ is stale)
npm run test:visual  # screenshots of every panel area (light, dark, mobile) vs baselines; serve the workbench on :8100 first
npm run test:visual:update   # accept an intended design change
```

## Conventions

- Components are anonymous Blade files in `resources/views/components`. Always use `$attributes->twMerge(...)`
  so call sites can override classes, and build Alpine expressions from props with `@js(...)`, never `'{{ }}'`.
- Panel styling only goes through Filament's `.fi-*` hooks and CSS variables, in `resources/css/partials/filament/`,
  using the `--lz-*` tokens. `FilamentContractTest` verifies every hook exists.
- Never put `backdrop-filter` on large fixed areas (see docs/performance.md).
- New appearance options belong in `config/lazyui.php` + `Support/Appearance.php`, not in hand-written CSS.
- Every bug fix comes with a test. `tests/Feature/ComponentsTest` lints every compiled Blade file.
- Update `CHANGELOG.md` under *Unreleased*.

## Releasing

1. Move *Unreleased* to a new version in `CHANGELOG.md` and bump `LazyUI::VERSION` and `window.LazyUI.version`.
2. `npm run build && composer test`, then commit.
3. `git tag v2.x.y && git push --tags`. Apps pick it up with `composer update lazyui/lazyui`.

Version rules: patch = fixes, minor = new components/options, major = breaking changes to component props,
config keys, Alpine API or the minimum Filament version.
