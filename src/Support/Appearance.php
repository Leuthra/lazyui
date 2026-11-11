<?php

namespace LazyUI\Support;

use Filament\Support\Colors\Color;
use Illuminate\Support\Str;

/**
 * Reads the appearance options from config/lazyui.php and turns them into
 * the runtime CSS (accent palettes, font families) and the client config.
 */
class Appearance
{
    public const MODES = ['auto', 'light', 'dark'];

    /**
     * @return array<string, array<int, string>> accent key => [shade => oklch colour]
     */
    public static function accents(): array
    {
        return static::memo('accents', function (): array {
            $accents = [];

            foreach ((array) config('lazyui.accents', []) as $key => $palette) {
                $accents[(string) $key] = is_string($palette)
                    ? Color::generatePalette($palette)
                    : array_map(fn (string|int $colour): string|int => is_string($colour) ? Color::convertToOklch($colour) : $colour, $palette);
            }

            return $accents ?: ['indigo' => Color::Indigo];
        });
    }

    /**
     * Results depend only on config, so they are computed once per config state (the settings
     * sheet and the CSS ask for the same palettes several times per request).
     */
    protected static function memo(string $key, callable $resolve): mixed
    {
        static $cache = [];

        $hash = $key.md5(serialize([config('lazyui.accents'), config('lazyui.fonts'), config('lazyui.defaults')]));

        return $cache[$hash] ??= $resolve();
    }

    /**
     * @return array<string, array{label: string, family: string, url: ?string}>
     */
    public static function fonts(): array
    {
        $fonts = [];

        foreach ((array) config('lazyui.fonts', []) as $key => $font) {
            $font = is_string($font) ? ['family' => $font] : (array) $font;

            $fonts[(string) $key] = [
                'label' => $font['label'] ?? Str::headline((string) $key),
                'family' => $font['family'] ?? 'ui-sans-serif, system-ui, sans-serif',
                'url' => $font['url'] ?? null,
            ];
        }

        return $fonts ?: ['sans' => ['label' => 'Sans', 'family' => 'ui-sans-serif, system-ui, sans-serif', 'url' => null]];
    }

    /**
     * Configured defaults, corrected to values that actually exist.
     *
     * @return array{mode: string, accent: string, font: string, aside: string}
     */
    public static function defaults(): array
    {
        $defaults = (array) config('lazyui.defaults', []);
        $accents = array_keys(static::accents());
        $fonts = array_keys(static::fonts());

        return [
            'mode' => in_array($defaults['mode'] ?? null, self::MODES, true) ? $defaults['mode'] : 'auto',
            'accent' => in_array($defaults['accent'] ?? null, $accents, true) ? $defaults['accent'] : $accents[0],
            'font' => in_array($defaults['font'] ?? null, $fonts, true) ? $defaults['font'] : $fonts[0],
            'aside' => ($defaults['aside'] ?? 'show') === 'hide' ? 'hide' : 'show',
        ];
    }

    /**
     * @return array<int, string> the default accent palette, e.g. for Filament's primary colour
     */
    public static function defaultAccentPalette(): array
    {
        return static::accents()[static::defaults()['accent']];
    }

    public static function tone(?string $tone = null): string
    {
        $tone ??= (string) config('lazyui.tone', 'mono');

        return $tone === 'accent' ? 'accent' : 'mono';
    }

    /**
     * Swatch colour shown in the appearance sheet.
     */
    public static function swatch(string $accent): string
    {
        return static::accents()[$accent][300] ?? static::accents()[$accent][400] ?? 'currentColor';
    }

    /**
     * Per-accent and per-font rules, keyed on attributes that theme.js sets on <html class="lazy">.
     */
    public static function css(): string
    {
        return static::memo('css', fn (): string => static::buildCss());
    }

    protected static function buildCss(): string
    {
        $css = '';

        foreach (static::accents() as $key => $palette) {
            $vars = '';

            foreach ($palette as $shade => $colour) {
                if ($safe = Str::sanitizeCssColor((string) $colour)) {
                    $vars .= "--color-accent-{$shade}:{$safe};";
                }
            }

            $css .= 'html.lazy[theme-accent="'.static::cssString($key).'"]{'.$vars.'}';
        }

        foreach (static::fonts() as $key => $font) {
            $family = str_replace(['{', '}', ';', '<'], '', $font['family']);
            $css .= 'html.lazy[theme-font="'.static::cssString($key).'"] body{font-family:'.$family.'}';
        }

        return $css;
    }

    /**
     * @param  array<int, string>  $except  font keys whose stylesheet is already loaded elsewhere
     * @return array<int, string>
     */
    public static function fontUrls(array $except = []): array
    {
        return collect(static::fonts())
            ->except($except)
            ->pluck('url')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * window.LazyUIConfig, read by resources/js/theme.js.
     *
     * @return array<string, mixed>
     */
    public static function clientConfig(): array
    {
        return [
            ...static::defaults(),
            'accents' => array_keys(static::accents()),
            'fonts' => array_keys(static::fonts()),
            'storageKey' => (string) config('lazyui.storage_key', 'lazy-settings'),
            'eventKey' => 'lazy-appearance-change',
            'darkModeShortcut' => (bool) config('lazyui.dark_mode_shortcut', true),
        ];
    }

    protected static function cssString(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', $value) ?? '';
    }
}
