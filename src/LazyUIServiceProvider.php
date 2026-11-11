<?php

namespace LazyUI;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use LazyUI\Commands\InstallCommand;
use LazyUI\Commands\PublishCommand;
use LazyUI\Support\Appearance;
use TailwindMerge\TailwindMerge;

class LazyUIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(LazyUI::path('config/lazyui.php'), 'lazyui');

        $this->app->singletonIf(TailwindMerge::class, fn (): TailwindMerge => TailwindMerge::instance());
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerAssets();
        $this->registerTwMerge();

        if ($this->app->runningInConsole()) {
            $this->registerAbout();
            $this->registerPublishing();
            $this->commands([InstallCommand::class, PublishCommand::class]);
        }
    }

    /**
     * <x-lazy::button>, <x-lazy::dropdown.item>, ...
     * Overridable per file in resources/views/vendor/lazy/components (php artisan lazy:publish).
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(LazyUI::path('resources/views'), 'lazy');
        Blade::anonymousComponentNamespace('lazy::components', 'lazy');
    }

    /**
     * Compiled assets from dist/. `php artisan filament:assets` copies them to public/
     * (Filament runs it on every `composer update` via filament:upgrade).
     * Loaded on request: <x-lazy::head /> (JS) and LazyUIPlugin (JS + CSS) decide where they go.
     */
    protected function registerAssets(): void
    {
        FilamentAsset::register([
            Css::make('lazyui-filament', LazyUI::path('dist/filament.css'))->loadedOnRequest(),
            Js::make('lazyui', LazyUI::path('dist/lazyui.js'))->loadedOnRequest(),
            Js::make('lazyui-filament', LazyUI::path('dist/lazyui-filament.js'))->loadedOnRequest(),
        ], LazyUI::PACKAGE);
    }

    /**
     * `php artisan about` shows whether the compiled assets are published, the usual deployment miss.
     */
    protected function registerAbout(): void
    {
        AboutCommand::add('LazyUI', fn (): array => [
            'Version' => LazyUI::VERSION,
            'Tone' => Appearance::tone(),
            'Accents' => implode(', ', array_keys(Appearance::accents())),
            'Assets' => collect(['css/lazyui/lazyui/lazyui-filament.css', 'js/lazyui/lazyui/lazyui-filament.js', 'js/lazyui/lazyui/lazyui.js', 'vendor/lazyui/image/cyan-blur.png'])
                ->every(fn (string $file): bool => is_file(public_path($file)))
                ? '<fg=green;options=bold>PUBLISHED</>'
                : '<fg=red;options=bold>MISSING</> (run php artisan filament:assets && php artisan vendor:publish --tag=lazyui-assets)',
        ]);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            LazyUI::path('config/lazyui.php') => config_path('lazyui.php'),
        ], 'lazyui-config');

        // Images and the optional FilePond / TinyMCE / ApexCharts / highlight.js builds.
        $this->publishes([
            LazyUI::path('public') => public_path('vendor/lazyui'),
        ], ['lazyui-assets', 'laravel-assets']);

        $this->publishes([
            LazyUI::path('resources/views/components') => resource_path('views/vendor/lazy/components'),
        ], 'lazyui-views');
    }

    /**
     * $attributes->twMerge(...) and @twMerge(...), API-compatible with gehrisandro/tailwind-merge-laravel
     * (used by the original LazyUI) without its Guzzle 7 requirement, so it installs on Laravel 13.
     */
    protected function registerTwMerge(): void
    {
        if (! ComponentAttributeBag::hasMacro('twMerge')) {
            ComponentAttributeBag::macro('twMerge', function (...$args): ComponentAttributeBag {
                /** @var ComponentAttributeBag $this */
                $custom = $this->get('class', '');

                // Fast path: nothing passed by the caller means nothing can conflict, so skip
                // TailwindMerge (its first call per request costs several milliseconds).
                $this->offsetSet('class', blank($custom)
                    ? Arr::toCssClasses(LazyUI::flattenClasses($args))
                    : LazyUI::twMerge($args, $custom));

                return $this;
            });
        }

        if (! function_exists('twMerge')) {
            Blade::directive('twMerge', fn (?string $expression): string => "<?php echo e(\\LazyUI\\LazyUI::twMerge({$expression})); ?>");
        }
    }
}
