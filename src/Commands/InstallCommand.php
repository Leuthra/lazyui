<?php

namespace LazyUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LazyUI\LazyUI;

class InstallCommand extends Command
{
    protected $signature = 'lazy:install
        {--config : Also publish config/lazyui.php}
        {--no-panels : Do not add LazyUIPlugin to Filament panel providers}
        {--no-css : Do not add the LazyUI import to resources/css/app.css}';

    protected $description = 'Install LazyUI: publish assets, register the plugin on every Filament panel, import the CSS';

    public function handle(): int
    {
        $this->call('filament:assets');
        $this->call('vendor:publish', ['--tag' => 'lazyui-assets', '--force' => true]);

        if ($this->option('config')) {
            $this->call('vendor:publish', ['--tag' => 'lazyui-config']);
        }

        $changes = [];

        if (! $this->option('no-panels')) {
            foreach (File::glob(app_path('Providers/Filament/*PanelProvider.php')) as $provider) {
                $changes[] = $this->registerPlugin($provider);
            }
        }

        if (! $this->option('no-css')) {
            $changes[] = $this->addImport(
                resource_path('css/app.css'),
                'resources/css/index.css',
                after: "@import 'tailwindcss';",
            );
        }

        $changes = array_values(array_filter($changes));

        if ($changes !== []) {
            $this->components->bulletList($changes);
        }

        $this->components->info('LazyUI is installed.');

        return self::SUCCESS;
    }

    /**
     * Adds ->plugin(LazyUIPlugin::make()) right after ->id(...) in a panel provider.
     * A panel with its own Vite theme gets the LazyUI source imported there and ->precompiled(false),
     * so LazyUI is compiled once, together with the theme.
     */
    protected function registerPlugin(string $file): ?string
    {
        $contents = File::get($file);

        if (str_contains($contents, 'LazyUIPlugin')) {
            return null;
        }

        if (! preg_match("/->id\\(\\s*['\"]([^'\"]+)['\"]\\s*\\)/", $contents, $id)) {
            $this->components->warn("Could not find ->id(...) in {$this->short($file)}. Add ->plugin(\\LazyUI\\Filament\\LazyUIPlugin::make()) manually.");

            return null;
        }

        $theme = resource_path("css/filament/{$id[1]}/theme.css");
        $useTheme = str_contains($contents, '->viteTheme(') && File::exists($theme);
        $plugin = '->plugin(\\LazyUI\\Filament\\LazyUIPlugin::make()'.($useTheme ? '->precompiled(false)' : '').')';

        File::put($file, preg_replace('/^(\s*)(->id\([^)]*\))\R/m', "$1$2\n$1{$plugin}\n", $contents, 1));

        if ($useTheme) {
            $this->addImport($theme, 'resources/css/filament.css', after: "vendor/filament/filament/resources/css/theme.css';");
        }

        return "{$this->short($file)}: {$plugin}".($useTheme ? " (+ import in {$this->short($theme)})" : '');
    }

    protected function addImport(string $file, string $packageFile, string $after): ?string
    {
        if (! File::exists($file)) {
            return null;
        }

        $contents = File::get($file);
        $line = "@import '".$this->relative(dirname($file), LazyUI::path($packageFile))."';";

        if (str_contains($contents, 'lazyui/'.$packageFile)) {
            return null;
        }

        $position = strpos($contents, $after);

        if ($position === false) {
            $this->components->warn("Add to {$this->short($file)}: {$line}");

            return null;
        }

        $end = strpos($contents, "\n", $position);
        $end = $end === false ? strlen($contents) : $end;

        File::put($file, substr($contents, 0, $end)."\n".$line.substr($contents, $end));

        return "{$this->short($file)}: {$line}";
    }

    protected function relative(string $from, string $to): string
    {
        // Point at vendor/ (not a symlink target) so the path keeps working for every install.
        $to = str_replace(LazyUI::path(), base_path('vendor/'.LazyUI::PACKAGE), $to);

        $from = explode('/', trim(str_replace('\\', '/', $from), '/'));
        $to = explode('/', trim(str_replace('\\', '/', $to), '/'));

        while ($from !== [] && $to !== [] && $from[0] === $to[0]) {
            array_shift($from);
            array_shift($to);
        }

        return str_repeat('../', count($from)).implode('/', $to);
    }

    protected function short(string $path): string
    {
        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
