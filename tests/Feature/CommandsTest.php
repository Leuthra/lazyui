<?php

namespace LazyUI\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use LazyUI\Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test_publish_copies_a_component_that_then_takes_precedence(): void
    {
        $target = resource_path('views/vendor/lazy/components/badge');

        try {
            $this->artisan('lazy:publish', ['components' => ['badge']])->assertSuccessful();
            $this->assertFileExists($target.'/index.blade.php');

            File::put($target.'/index.blade.php', '<span>custom badge</span>');
            $this->assertStringContainsString('custom badge', Blade::render('<x-lazy::badge>New</x-lazy::badge>'));
        } finally {
            File::deleteDirectory(resource_path('views/vendor/lazy'));
        }
    }

    public function test_publish_rejects_unknown_components(): void
    {
        $this->artisan('lazy:publish', ['components' => ['nope']])->assertFailed();
    }

    public function test_install_registers_the_plugin_on_panel_providers(): void
    {
        $provider = app_path('Providers/Filament/ShopPanelProvider.php');
        File::ensureDirectoryExists(dirname($provider));
        File::put($provider, <<<'PHP'
            <?php
            class ShopPanelProvider {
                public function panel($panel) {
                    return $panel
                        ->id('shop')
                        ->path('shop');
                }
            }
            PHP);

        try {
            $this->artisan('lazy:install', ['--no-css' => true])->assertSuccessful();
            $this->assertStringContainsString("->id('shop')\n            ->plugin(\\LazyUI\\Filament\\LazyUIPlugin::make())", File::get($provider));

            // Idempotent.
            $this->artisan('lazy:install', ['--no-css' => true])->assertSuccessful();
            $this->assertSame(1, substr_count(File::get($provider), 'LazyUIPlugin'));
        } finally {
            File::delete($provider);
            $this->cleanPublishedAssets();
        }
    }

    public function test_install_compiles_lazyui_into_an_existing_panel_theme(): void
    {
        $provider = app_path('Providers/Filament/BlogPanelProvider.php');
        $theme = resource_path('css/filament/blog/theme.css');
        File::ensureDirectoryExists(dirname($provider));
        File::ensureDirectoryExists(dirname($theme));
        File::put($theme, "@import '../../../../vendor/filament/filament/resources/css/theme.css';\n");
        File::put($provider, <<<'PHP'
            <?php
            class BlogPanelProvider {
                public function panel($panel) {
                    return $panel
                        ->id('blog')
                        ->viteTheme('resources/css/filament/blog/theme.css');
                }
            }
            PHP);

        try {
            $this->artisan('lazy:install', ['--no-css' => true])->assertSuccessful();

            $this->assertStringContainsString('LazyUIPlugin::make()->precompiled(false)', File::get($provider));
            $this->assertStringContainsString("vendor/lazyui/lazyui/resources/css/filament.css';", File::get($theme));
        } finally {
            File::delete($provider);
            File::deleteDirectory(resource_path('css/filament/blog'));
            $this->cleanPublishedAssets();
        }
    }

    public function test_install_imports_lazyui_into_app_css(): void
    {
        $css = resource_path('css/app.css');
        $original = File::exists($css) ? File::get($css) : null;
        File::ensureDirectoryExists(dirname($css));
        File::put($css, "@import 'tailwindcss';\n");

        try {
            $this->artisan('lazy:install', ['--no-panels' => true])->assertSuccessful();
            $this->assertSame("@import 'tailwindcss';\n@import '../../vendor/lazyui/lazyui/resources/css/index.css';\n", File::get($css));
        } finally {
            $original === null ? File::delete($css) : File::put($css, $original);
            $this->cleanPublishedAssets();
        }
    }

    /**
     * Published assets are left in place on purpose: Testbench's public/ is shared with the
     * workbench server, and publishing is idempotent.
     */
    protected function cleanPublishedAssets(): void {}
}
