# Components

All components are anonymous Blade components under the `lazy::` prefix. Every component merges
your `class` with its own through `twMerge`, so `class="h-12"` replaces the default height instead
of fighting it. Any other attribute (`wire:model`, `x-on:click`, `id`, ...) is forwarded to the
main element.

Render `lazy::demo` to see them all: `Route::view('/lazyui', 'lazy::demo')`.

## Actions

### button
`href` (renders `<a>`), `type=button`, `variant` = `default|secondary|outline|ghost|destructive|link`,
`size` = `default|sm|lg|icon`.

```blade
<x-lazy::button variant="outline" size="sm" wire:click="save">Save</x-lazy::button>
<x-lazy::button href="/docs" variant="link">Docs</x-lazy::button>
```

### badge
`variant` = `default|secondary|outline|destructive`.

### dropdown
`dropdown`, `dropdown.trigger`, `dropdown.content` (`align=start|center|end`, `side=bottom|top|left|right`, `sideOffset=4`),
`dropdown.item` / `dropdown.link` (`disabled`, `inset`), `dropdown.label`, `dropdown.separator`, `dropdown.shortcut`,
`dropdown.group`, `dropdown.close`, `dropdown.sub` + `dropdown.sub.trigger` + `dropdown.sub.content`.
Keyboard navigation (arrows, Home/End, Esc) is built in.

```blade
<x-lazy::dropdown>
    <x-lazy::dropdown.trigger><x-lazy::button>Menu</x-lazy::button></x-lazy::dropdown.trigger>
    <x-lazy::dropdown.content align="end" class="w-48">
        <x-lazy::dropdown.item wire:click="rename">Rename <x-lazy::dropdown.shortcut>F2</x-lazy::dropdown.shortcut></x-lazy::dropdown.item>
        <x-lazy::dropdown.separator />
        <x-lazy::dropdown.link href="/logout">Log out</x-lazy::dropdown.link>
    </x-lazy::dropdown.content>
</x-lazy::dropdown>
```

## Overlays

### dialog
`id` (required), `isOpen`, `isFullscreen`, `backdropStatic` (Esc/backdrop doesn't close it), `backdropClass`,
`scrollbar` (keep page scroll), `level` (stacking), `class` (card). Parts: `dialog.header` (fullscreen + close buttons), `dialog.body`.

Open/close: `x-dialog:toggle="id"`, `x-dialog:open="id"`, `x-dialog:close` (inside: no id needed), or `$dialog.open('id')`.

```blade
<x-lazy::button x-data x-dialog:toggle="editUser">Edit</x-lazy::button>
<x-lazy::dialog id="editUser">
    <x-lazy::dialog.header />
    <x-lazy::dialog.body class="px-6 pb-6">...</x-lazy::dialog.body>
</x-lazy::dialog>
```

### sheet
`id` (required), `placement=right|left|top|bottom`, `size=350px` (capped to the screen), `isOpen`, `scrollbar`,
`backdropClass`, `class`. Parts: `sheet.header` (`title`), `sheet.body`, `sheet.footer`.
Open/close: `x-sheet:toggle="id"`, `x-sheet:open`, `x-sheet:close`, `$store.sheet.open('id')`.

### popover
Slots `toggle` and `content`. `placement` (any Floating UI placement, e.g. `bottom-start`), `offset=8`, `trigger=click|hover`.

### toast
Place `<x-lazy::toast />` once per page (layouts and Filament panels already do). `placement` = `top-right|top-left|top-center|bottom-*`,
`max=4`, `duration=3000`. Then:

```js
toast('Hello'); toast.success({ title: 'Saved', message: 'All good' });
const t = toast.loading('Uploading…'); t.success('Done');
```
```php
$this->dispatch('toast', type: 'success', title: 'Saved');   // Livewire
return back()->with('toast', ['type' => 'success', 'title' => 'Saved']);   // redirect
```

### alert (JS only)
`zalert({ type: 'warning', title, text, confirmText, cancelText, onConfirm, onCancel })`.
`title` and `text` are rendered as HTML, so escape user input.

## Forms

| Component | Props | Notes |
|---|---|---|
| `input` | `type=text` | |
| `textarea` | | |
| `label` | | |
| `checkbox` | `variant=default\|accent\|custom` | |
| `radio`, `radio.group` + `radio.item` | `variant`; group: `name`, `defaultValue` | group is `x-modelable` |
| `switch` | `size=xxs\|xs\|sm\|base\|lg`, `variant=default\|accent` | attributes go to the hidden checkbox |
| `select` + `select.option` | `displayIcon`, `searchInput=true` | options take `icon`, `label`, `description` slots; native `<select>` underneath, so `name`/`wire:model` work |
| `select.multiple` | `placeholder`, `max=0`, `checkbox` | |
| `select.ssr`, `select.multiple.ssr` | `url` (+ above) | fetches `url?search=` and expects `[{value,label,...}]` |
| `autocomplete` | `suggestions=[]` | free text with suggestions |
| `autocomplete.multiple` | `suggestions`, `max`, `checkbox`, `placeholder` | tag input; comma-separated value |
| `autocomplete.ssr` | `url` | |
| `form` | `id`, `action`, `method=POST`, `toast=true`, `toastErrors=summary\|detailed` | AJAX submit with toasts; dispatches a `form` window event (`detail.id`, `ok`, `data`) |
| `filepond` | `name`, `existingFiles`, `base_url`, `maxPreviewSize`, `multiple`, `accept`, `max` | needs `vendor:publish --tag=lazyui-assets` |
| `tinymce` | `id`, `options`, `toolbar`, `plugins` | slot = initial HTML; follows dark mode |

## Content & layout

- `card`: surface with border and shadow.
- `tabs` → `tabs.buttons` (`scrollable`, `classMarker`) → `tabs.button` (`isActive`, `classActive`), plus `tabs.contents` → `tabs.content`.
- `accordion` (`type="multiple"`, `collapsible`, `defaultValue`) → `accordion.item` (`value`) → `accordion.trigger` / `accordion.content`.
- `avatar` → `avatar.image` / `avatar.fallback` (the fallback shows when the image fails).
- `breadcrumb` → `breadcrumb.item`, `breadcrumb.link`, `breadcrumb.separator`, `breadcrumb.ellipsis`.
- `nav.section` (`label`, `isOpen`, `hideIf`), `nav.item` (`isActive`, `hideIf`; slots `icon`, `title`, `description`, `span`),
  `nav.item-collapse` (slots `icon`, `title`, `sub`), `nav.sub-item` (slot `title`).

## Page shell

- `head`: appearance bootstrap plus the precompiled JS. Props `assets=true` (load `lazyui.js`), `fonts=true`, `exceptFonts=[]`.
- `layout`: full HTML page. Prop `title`, `vite` (defaults to `config('lazyui.vite')`). Slots `menu`, `brand`, `actions`, `user`.
- `layout.sidebar`, `layout.header` (slots `start`, `user`), `layout.settings`, `settings-button`.

## Alpine API

| | |
|---|---|
| `$appearance.setTheme('light'\|'dark'\|'auto'\|'toggle')`, `.setAccent(key)`, `.setFont(key)`, `.setAside('toggle')`, `.resetAppearance()`, `.state`, `.isDark()` | appearance store (`window.LazySettings`) |
| `$dialog`, `$store.sheet` | `open(id)`, `close(id)`, `toggle(id)`, `isOpen(id)` |
| `$store.lazySidebar` | front-end mobile sidebar |
| `x-tooltip="{ text: 'Hi' }"` | tippy.js tooltip (front end only; Filament pages keep Filament's tooltip) |
| `lazy-appearance-change` window event | fired when appearance changes (`detail` = state) |
