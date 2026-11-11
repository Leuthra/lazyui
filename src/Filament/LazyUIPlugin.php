<?php

namespace LazyUI\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use LazyUI\Support\Appearance;

/**
 * Gives a Filament panel the LazyUI look and runtime.
 *
 *   $panel->plugin(LazyUIPlugin::make());
 *
 * Without a custom panel theme nothing else is needed: the precompiled CSS/JS ship in dist/
 * and are published by `php artisan filament:assets`.
 *
 * With a custom theme (php artisan make:filament-theme), import the source instead and say so:
 *
 *   @import '../../../../vendor/lazyui/lazyui/resources/css/filament.css';   // in theme.css
 *   LazyUIPlugin::make()->precompiled(false)
 * so LazyUI is compiled once, together with your theme (lazy:install does both).
 */
class LazyUIPlugin implements Plugin
{
    protected bool $precompiled = true;

    protected bool $settingsButton = true;

    protected bool $palette = true;

    protected ?string $tone = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'lazyui';
    }

    /**
     * Load the precompiled dist/filament.css. Pass false when the panel's own Vite theme
     * imports vendor/lazyui/lazyui/resources/css/filament.css.
     */
    public function precompiled(bool $condition = true): static
    {
        $this->precompiled = $condition;

        return $this;
    }

    public function isPrecompiled(): bool
    {
        return $this->precompiled;
    }

    /** Gear button next to the user menu that opens the appearance sheet. */
    public function settingsButton(bool $condition = true): static
    {
        $this->settingsButton = $condition;

        return $this;
    }

    /** Use the LazyUI gray ("cat") and the default accent as Filament colours. */
    public function palette(bool $condition = true): static
    {
        $this->palette = $condition;

        return $this;
    }

    /** 'mono' (black/white solid buttons and controls) or 'accent'. Defaults to config('lazyui.tone'). */
    public function tone(string $tone): static
    {
        $this->tone = $tone;

        return $this;
    }

    public function getTone(): string
    {
        return Appearance::tone($this->tone);
    }

    public function register(Panel $panel): void
    {
        if ($this->palette) {
            // Registered first, so a later ->colors([...]) in the panel provider still wins.
            $panel->colors([
                'gray' => static::grayPalette(),
                'primary' => Appearance::defaultAccentPalette(),
            ]);
        }

        $panel
            ->renderHook(PanelsRenderHook::HEAD_START, fn (): string => view('lazy::filament.head', ['tone' => $this->getTone()])->render())
            ->renderHook(PanelsRenderHook::SCRIPTS_BEFORE, fn (): string => view('lazy::filament.scripts')->render())
            ->renderHook(PanelsRenderHook::BODY_END, fn (): string => view('lazy::filament.body-end')->render());

        if ($this->precompiled) {
            // After Filament's theme, so equal-specificity rules in the same cascade layer win.
            $panel->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): string => view('lazy::filament.styles')->render());
        }

        if ($this->settingsButton) {
            $panel->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn (): string => view('lazy::filament.settings-button')->render());
        }
    }

    public function boot(Panel $panel): void {}

    /**
     * LazyUI's "cat" slate, extended with a 950 shade for Filament.
     *
     * @return array<int, string>
     */
    public static function grayPalette(): array
    {
        return [
            50 => 'oklch(0.99 0.0011 197.14)',
            100 => 'oklch(0.98 0.0017 247.84)',
            200 => 'oklch(0.97 0.0034 247.86)',
            300 => 'oklch(0.91 0.0081 253.86)',
            400 => 'oklch(0.84 0.0149 244.75)',
            500 => 'oklch(0.69 0.0242 248.18)',
            600 => 'oklch(0.55 0.0291 244.76)',
            700 => 'oklch(0.42 0.0238 253.08)',
            800 => 'oklch(0.26 0.0213 248.65)',
            900 => 'oklch(0.22 0.0165 252.5)',
            950 => 'oklch(0.18 0.0145 252.5)',
        ];
    }
}
