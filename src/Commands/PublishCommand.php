<?php

namespace LazyUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LazyUI\LazyUI;

use function Laravel\Prompts\multiselect;

class PublishCommand extends Command
{
    protected $signature = 'lazy:publish
        {components?* : Component names, e.g. button dropdown}
        {--all : Publish every component}
        {--force : Overwrite files that were already published}';

    protected $description = 'Copy LazyUI components into resources/views/vendor/lazy/components so you can edit them';

    public function handle(): int
    {
        $available = LazyUI::components();

        $components = $this->option('all')
            ? $available
            : ($this->argument('components') ?: multiselect(
                label: 'Which components do you want to publish?',
                options: $available,
                required: true,
                scroll: 15,
            ));

        $unknown = array_diff($components, $available);

        if ($unknown !== []) {
            $this->components->error('Unknown component(s): '.implode(', ', $unknown));
            $this->line('  Available: '.implode(', ', $available));

            return self::FAILURE;
        }

        $published = [];

        foreach ($components as $component) {
            $source = LazyUI::path("resources/views/components/{$component}");
            $target = resource_path("views/vendor/lazy/components/{$component}");

            if (File::isFile($source.'.blade.php')) {
                $source .= '.blade.php';
                $target .= '.blade.php';
            }

            if (File::exists($target) && ! $this->option('force')) {
                $this->components->warn("{$component} already published (use --force to overwrite).");

                continue;
            }

            if (File::isDirectory($source)) {
                File::copyDirectory($source, $target);
            } else {
                File::ensureDirectoryExists(dirname($target));
                File::copy($source, $target);
            }

            $published[] = 'resources/views/vendor/lazy/components/'.basename($target);
        }

        if ($published !== []) {
            $this->components->info('Published. Your copies now take precedence over the package.');
            $this->components->bulletList($published);
        }

        return self::SUCCESS;
    }
}
