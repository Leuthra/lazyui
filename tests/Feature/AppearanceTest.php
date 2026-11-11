<?php

namespace LazyUI\Tests\Feature;

use Filament\Support\Colors\Color;
use LazyUI\Support\Appearance;
use LazyUI\Tests\TestCase;

class AppearanceTest extends TestCase
{
    public function test_accent_css_is_generated_from_config(): void
    {
        config(['lazyui.accents.teal' => Color::Teal]);

        $css = Appearance::css();

        $this->assertStringContainsString('html.lazy[theme-accent="teal"]{--color-accent-50:', $css);
        $this->assertStringContainsString('html.lazy[theme-accent="indigo"]', $css);
        $this->assertContains('teal', Appearance::clientConfig()['accents']);
    }

    public function test_single_colour_accent_gets_a_full_palette(): void
    {
        config(['lazyui.accents' => ['brand' => '#0ea5e9']]);

        $this->assertCount(11, Appearance::accents()['brand']);
        $this->assertSame('brand', Appearance::defaults()['accent']);
    }

    public function test_invalid_defaults_fall_back_to_existing_values(): void
    {
        config(['lazyui.defaults' => ['mode' => 'neon', 'accent' => 'nope', 'font' => 'nope']]);

        $defaults = Appearance::defaults();

        $this->assertSame('auto', $defaults['mode']);
        $this->assertSame(array_key_first(Appearance::accents()), $defaults['accent']);
        $this->assertSame(array_key_first(Appearance::fonts()), $defaults['font']);
    }

    public function test_css_cannot_be_broken_out_of(): void
    {
        config([
            'lazyui.accents' => ['x"]{}body{' => '#000'],
            'lazyui.fonts' => ['evil' => ['family' => 'Arial;}</style><script>alert(1)</script>']],
        ]);

        $css = Appearance::css();

        $this->assertStringNotContainsString('</style>', $css);
        $this->assertStringNotContainsString('body{display', $css);
        $this->assertStringContainsString('[theme-accent="xbody"]', $css);
    }

    public function test_settings_sheet_lists_configured_accents_and_fonts(): void
    {
        config([
            'lazyui.accents' => ['teal' => Color::Teal, 'rose' => Color::Rose],
            'lazyui.fonts' => ['inter' => 'Inter', 'serif' => ['label' => 'Bookish', 'family' => 'serif']],
        ]);

        $html = view('lazy::components.layout.settings')->render();

        $this->assertStringContainsString("setAccent('teal')", $html);
        $this->assertStringContainsString("setAccent('rose')", $html);
        $this->assertStringNotContainsString("setAccent('indigo')", $html);
        $this->assertStringContainsString('Bookish', $html);
    }
}
