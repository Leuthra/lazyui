# When Filament updates

LazyUI changes the look of Filament from the outside: it only uses Filament's public CSS hooks (`.fi-*` classes),
its CSS variables, its render hooks and its dark-mode storage key. That surface is small and checked automatically.

## What protects you

| Check | Catches | Where |
|---|---|---|
| `FilamentContractTest` | a `.fi-*` class, CSS variable, render hook or theme key LazyUI uses no longer exists; Filament starts using an Alpine store/directive name LazyUI owns | `composer test` |
| Visual regression | the panel *looks* different (spacing, colours, broken layout) in light, dark or mobile | `npm run test:visual` |
| Overflow + JS error tests | horizontal scroll on phones, JavaScript errors on the main pages | part of `npm run test:visual` |
| Weekly CI run | all of the above against the newest Filament release, every Monday | `.github/workflows/tests.yml` |
| Lowest-version CI job | LazyUI still works on the oldest Filament version `composer.json` allows | same workflow |

## Version policy

`composer.json` requires `filament/filament: ^5.0`. Apps only get Filament 5.x minors and patches, which keep the
public hooks stable. A new Filament major (6.x) needs a LazyUI release that widens the constraint, so apps can't
jump to it by accident.

## Playbook

1. **CI is red on the weekly run** (or Filament announced a release). Locally:
   ```bash
   composer update filament/filament
   composer test
   ```
2. **Contract test failed.** The message names the missing class/hook and the file that uses it.
   Find the new name in Filament's source (`vendor/filament/*/resources/css`, `resources/views`) and update
   `resources/css/partials/filament/<file>.css`, or `src/Filament/LazyUIPlugin.php` for render hooks.
3. **Visual test failed.** Open the report (`npx playwright show-report`) and compare expected, actual and diff.
   - Unintended change → fix the CSS.
   - Intended (Filament improved something you want to keep) → `npm run test:visual:update` locally, and run the
     *update visual baselines* workflow for CI.
4. `npm run build` and commit `dist/`. Add a CHANGELOG entry ("Compatible with Filament 5.x").
5. Tag a patch release. Apps get it with `composer update lazyui/lazyui`.

## Local workbench

```bash
composer workbench     # fresh database with demo data + publish assets
composer serve         # http://127.0.0.1:8000/admin   admin@lazyui.test / password
```

The workbench panel (`workbench/app`) uses every Filament feature: forms with all field types, tables with filters,
grouping, summaries, bulk and row actions, infolists, wizard, repeater, builder, relation manager, modals, slide-overs,
notifications, widgets, global search, profile, collapsible sidebar. Add a feature there whenever LazyUI starts
styling it, then add a scene to `tests/visual/panel.spec.ts`.

Use `testbench.yaml` (gitignored, copied from `testbench.yaml.dist`) to point it at MySQL instead of SQLite.
Set the cookie `lazyui_bench=off` to render the same panel without LazyUI, which is handy for comparisons.
