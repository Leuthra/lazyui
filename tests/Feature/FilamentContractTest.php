<?php

namespace LazyUI\Tests\Feature;

use Filament\View\PanelsRenderHook;
use LazyUI\Tests\TestCase;

/**
 * Everything LazyUI relies on from Filament, checked against the installed Filament version.
 *
 * When Filament renames or removes one of these, this test names exactly what broke,
 * before anyone sees a half-styled panel. Run it after every Filament update (CI does weekly).
 */
class FilamentContractTest extends TestCase
{
    public function test_every_fi_class_used_by_the_panel_css_still_exists_in_filament(): void
    {
        $used = [];

        foreach (glob($this->packagePath('resources/css/partials/filament/*.css')) as $file) {
            preg_match_all('/\.(fi-[a-z0-9-]+)/', file_get_contents($file), $matches);

            foreach ($matches[1] as $class) {
                $used[$class][] = basename($file);
            }
        }

        $this->assertNotEmpty($used);

        $haystack = $this->filamentSource(['resources/views', 'resources/css', 'src']);
        $missing = array_filter(array_keys($used), fn (string $class): bool => ! str_contains($haystack, $class));

        $this->assertSame([], array_values($missing), 'Filament no longer has these classes (used in '.implode(', ', array_unique(array_merge(...array_values(array_intersect_key($used, array_flip($missing)))))).'). Update resources/css/partials/filament/.');
    }

    public function test_css_variables_lazyui_overrides_still_exist(): void
    {
        $css = $this->filamentSource(['resources/css']);

        foreach (['--bg', '--text', '--hover-bg', '--hover-text', '--dark-bg', '--dark-text', '--dark-hover-bg', '--dark-hover-text', '--primary-500', '--danger-600', '--color-500'] as $variable) {
            $this->assertStringContainsString($variable, $css, "Filament CSS no longer uses {$variable}.");
        }
    }

    public function test_render_hooks_used_by_the_plugin_exist(): void
    {
        foreach (['HEAD_START', 'STYLES_AFTER', 'SCRIPTS_BEFORE', 'USER_MENU_BEFORE', 'BODY_END'] as $hook) {
            $this->assertTrue(defined(PanelsRenderHook::class.'::'.$hook), "PanelsRenderHook::{$hook} is gone.");
        }
    }

    public function test_theme_sync_contract_with_filament_dark_mode(): void
    {
        $source = $this->filamentSource(['resources/js', 'resources/views']);

        $this->assertStringContainsString("'filament:theme:mode'", $source, 'Filament changed its dark-mode storage key (resources/js/theme.js relies on it).');
        $this->assertStringContainsString('theme-changed', $source, 'Filament no longer listens to the theme-changed event (resources/js/theme.js dispatches it).');
    }

    public function test_lazyui_alpine_names_do_not_collide_with_filament(): void
    {
        $js = $this->filamentSource(['resources/js', 'resources/views', 'dist']);

        foreach (['lazySidebar', 'sheet', 'dialog', 'lazy-settings'] as $store) {
            $this->assertDoesNotMatchRegularExpression('/Alpine\.store\(\s*[\'"]'.preg_quote($store, '/').'[\'"]/', $js, "Filament now registers the Alpine store '{$store}', which LazyUI also uses.");
        }

        foreach (['dropdown-menu', 'accordion', 'sheet', 'dialog'] as $directive) {
            $this->assertDoesNotMatchRegularExpression('/Alpine\.directive\(\s*[\'"]'.preg_quote($directive, '/').'[\'"]/', $js, "Filament now registers x-{$directive}, which LazyUI also uses.");
        }

        $this->assertDoesNotMatchRegularExpression('/Alpine\.magic\(\s*[\'"](appearance|dialog|sheet)[\'"]/', $js, 'Filament now registers a magic LazyUI uses ($appearance, $dialog or $sheet).');
    }

    /**
     * @param  array<int, string>  $dirs  directories inside each vendor/filament/* package
     */
    protected function filamentSource(array $dirs): string
    {
        static $cache = [];

        $key = implode('|', $dirs);

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $source = '';

        foreach (glob($this->packagePath('vendor/filament/*'), GLOB_ONLYDIR) as $package) {
            foreach ($dirs as $dir) {
                if (! is_dir("{$package}/{$dir}")) {
                    continue;
                }

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("{$package}/{$dir}", \FilesystemIterator::SKIP_DOTS));

                foreach ($files as $file) {
                    if (preg_match('/\.(php|css|js)$/', $file->getFilename())) {
                        $source .= file_get_contents($file->getPathname())."\n";
                    }
                }
            }
        }

        return $cache[$key] = $source;
    }

    protected function packagePath(string $path): string
    {
        return dirname(__DIR__, 2).'/'.$path;
    }
}
