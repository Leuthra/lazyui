<?php

namespace LazyUI;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Arr;
use TailwindMerge\TailwindMerge;

class LazyUI
{
    public const VERSION = '2.0.0';

    /** Composer package name; also the FilamentAsset package key. */
    public const PACKAGE = 'lazyui/lazyui';

    public static function path(string $path = ''): string
    {
        return dirname(__DIR__).($path !== '' ? DIRECTORY_SEPARATOR.ltrim($path, '/\\') : '');
    }

    /**
     * URL of the precompiled panel stylesheet (dist/filament.css), published by `php artisan filament:assets`.
     */
    public static function styleUrl(): string
    {
        return FilamentAsset::getStyleHref('lazyui-filament', static::PACKAGE);
    }

    /**
     * @param  'lazyui'|'lazyui-filament'  $id  front-end bundle (with tooltips) or the slimmer panel bundle
     */
    public static function scriptUrl(string $id = 'lazyui'): string
    {
        return FilamentAsset::getScriptSrc($id, static::PACKAGE);
    }

    /**
     * The appearance bootstrap script (minified build), inlined in <head> to avoid a flash of the wrong theme.
     */
    public static function themeScript(): string
    {
        static $script;

        $file = is_file(static::path('dist/theme.js')) ? 'dist/theme.js' : 'resources/js/theme.js';

        return $script ??= trim((string) file_get_contents(static::path($file)));
    }

    /**
     * Top-level component names (button, dropdown, layout, ...), for lazy:publish.
     *
     * @return list<string>
     */
    public static function components(): array
    {
        return collect(scandir(static::path('resources/views/components')) ?: [])
            ->reject(fn (string $entry): bool => str_starts_with($entry, '.'))
            ->map(fn (string $entry): string => str_replace('.blade.php', '', $entry))
            ->values()
            ->all();
    }

    /**
     * Merge Tailwind classes; later classes win. Accepts strings, lists and
     * conditional arrays (['pl-8' => $inset]) like Arr::toCssClasses().
     */
    public static function twMerge(mixed ...$args): string
    {
        static $memo = [];

        $classes = Arr::toCssClasses(static::flattenClasses($args));

        return $memo[$classes] ??= app(TailwindMerge::class)->merge($classes);
    }

    /**
     * @param  array<int|string, mixed>  $args
     * @return array<int|string, mixed>
     */
    public static function flattenClasses(array $args): array
    {
        $result = [];

        foreach ($args as $key => $value) {
            if (is_array($value)) {
                foreach (static::flattenClasses($value) as $innerKey => $innerValue) {
                    is_int($innerKey) ? $result[] = $innerValue : $result[$innerKey] = $innerValue;
                }
            } elseif (is_int($key)) {
                $result[] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
