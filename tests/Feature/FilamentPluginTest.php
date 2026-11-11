<?php

namespace LazyUI\Tests\Feature;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Vite;
use LazyUI\Filament\LazyUIPlugin;
use LazyUI\Support\Appearance;
use LazyUI\Tests\TestCase;

class FilamentPluginTest extends TestCase
{
    public function test_login_page_has_lazyui_runtime_and_styles(): void
    {
        $html = $this->get('/admin/login')->assertOk()->getContent();

        $this->assertStringContainsString('window.LazyUIConfig', $html);
        $this->assertStringContainsString('id="lazySettings"', $html);
        $this->assertStringContainsString('lazyui-filament.css', $html);
        $this->assertStringContainsString('js/lazyui/lazyui/lazyui-filament.js', $html);
        $this->assertStringNotContainsString('js/lazyui/lazyui/lazyui.js', $html);

        // The restyle sheet must come after Filament's own theme CSS to win the cascade.
        $this->assertGreaterThan(
            strpos($html, 'filament/filament/app.css') ?: strpos($html, '/css/filament/filament/'),
            strpos($html, 'lazyui-filament.css'),
        );
    }

    public function test_panel_with_own_theme_skips_the_precompiled_stylesheet(): void
    {
        $html = $this->get('/themed/login')->assertOk()->getContent();

        $this->assertStringNotContainsString('lazyui-filament.css', $html);
        $this->assertStringContainsString('window.LazyUIConfig', $html);
        $this->assertStringContainsString('js/lazyui/lazyui/lazyui-filament.js', $html);
    }

    public function test_tone_defaults_to_mono_and_can_be_switched(): void
    {
        $this->assertStringContainsString("dataset.lazyTone = 'mono'", $this->get('/admin/login')->getContent());

        config(['lazyui.tone' => 'accent']);
        $this->assertStringContainsString("dataset.lazyTone = 'accent'", view('lazy::components.head')->render());

        $this->assertSame('accent', LazyUIPlugin::make()->tone('accent')->getTone());
        $this->assertSame('mono', LazyUIPlugin::make()->tone('nonsense')->getTone());
    }

    public function test_inline_tags_carry_the_vite_csp_nonce(): void
    {
        Vite::useCspNonce('n0nce');

        $html = view('lazy::components.head')->render();

        $this->assertSame(3, substr_count($html, 'nonce="n0nce"'));
    }

    public function test_panel_uses_lazyui_palette(): void
    {
        $colors = Filament::getPanel('admin')->getColors();

        $this->assertSame(LazyUIPlugin::grayPalette(), $colors['gray']);
        $this->assertSame(Appearance::defaultAccentPalette(), $colors['primary']);
    }

    public function test_frontend_head_loads_the_runtime_but_leaves_css_to_the_app_build(): void
    {
        $html = view('lazy::components.head')->render();

        $this->assertStringContainsString('js/lazyui/lazyui/lazyui.js', $html);
        $this->assertStringNotContainsString('.css" data-lazyui', $html);
        $this->assertStringContainsString('fonts.bunny.net', $html);
    }

    public function test_dist_assets_exist(): void
    {
        foreach (['dist/filament.css', 'dist/lazyui.js', 'dist/lazyui-filament.js', 'dist/theme.js'] as $file) {
            $this->assertFileExists(dirname(__DIR__, 2).'/'.$file, "Run `npm run build` ({$file} missing).");
        }
    }
}
