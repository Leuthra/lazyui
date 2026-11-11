<?php

namespace LazyUI\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use LazyUI\Tests\TestCase;

class ComponentsTest extends TestCase
{
    public function test_playground_renders_every_component(): void
    {
        $html = view('lazy::demo')->render();

        $this->assertStringNotContainsString('<x-lazy::', $html);
        $this->assertStringContainsString('window.LazyUIConfig', $html);
        $this->assertStringContainsString('id="lazySettings"', $html);
        $this->assertStringContainsString('data-lazy-toast', $html);
        $this->assertStringContainsString('id="demoDialog"', $html);
    }

    public function test_every_component_file_compiles(): void
    {
        $files = collect(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__DIR__, 2).'/resources/views')))
            ->filter(fn (\SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.blade.php'))
            ->map(fn (\SplFileInfo $file): string => $file->getPathname())
            ->all();

        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $php = Blade::compileString(file_get_contents($file));
            $tmp = tempnam(sys_get_temp_dir(), 'lazyui');
            file_put_contents($tmp, $php);
            exec('php -l '.escapeshellarg($tmp).' 2>&1', $output, $code);
            unlink($tmp);

            $this->assertSame(0, $code, basename(dirname($file)).'/'.basename($file).': '.implode("\n", $output));
        }
    }

    public function test_class_attribute_overrides_component_defaults(): void
    {
        $html = Blade::render('<x-lazy::button class="h-12 rounded-full">Go</x-lazy::button>');

        $this->assertStringContainsString('h-12', $html);
        $this->assertStringContainsString('rounded-full', $html);
        $this->assertStringNotContainsString('h-9', $html);
        $this->assertStringNotContainsString('rounded-md', $html);
    }

    public function test_falsey_props_render_valid_alpine_expressions(): void
    {
        $html = Blade::render('<x-lazy::nav.section label="Menu" :is-open="false">x</x-lazy::nav.section>');

        $this->assertStringContainsString('expanded: false', $html);
    }

    public function test_user_content_is_escaped_in_alpine_expressions(): void
    {
        $html = Blade::render('<x-lazy::accordion.item value="it\'s &quot;fine&quot;">x</x-lazy::accordion.item>');

        $this->assertStringNotContainsString("item: 'it's", $html);
    }
}
