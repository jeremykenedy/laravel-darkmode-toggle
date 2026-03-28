<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'darkmode:install
        {--css=tailwind : CSS framework (tailwind, bootstrap5, bootstrap4)}
        {--frontend=blade : Frontend framework (blade, livewire, vue, react, svelte)}
        {--no-config : Skip publishing config}';

    protected $description = 'Install the dark mode toggle package';

    public function handle(): int
    {
        $css = $this->option('css');
        $frontend = $this->option('frontend');

        $this->info("Installing dark mode toggle with {$css} + {$frontend}...");

        if (!$this->option('no-config')) {
            $this->call('vendor:publish', ['--tag' => 'darkmode-config']);
        }

        $this->call('vendor:publish', ['--tag' => 'darkmode-views', '--force' => true]);

        if (!in_array($frontend, ['blade', 'livewire'])) {
            $tag = 'darkmode-'.$frontend;
            $this->call('vendor:publish', ['--tag' => $tag, '--force' => true]);
            $this->info("Published {$frontend} components.");
        }

        $this->newLine();
        $this->info('Dark mode toggle installed.');
        $this->newLine();
        $this->line('Usage in Blade:');
        $this->line('  <x-darkmode-toggle />');
        $this->newLine();
        $this->line('Usage in Livewire:');
        $this->line('  <livewire:darkmode-toggle />');
        $this->newLine();
        $this->line('Add this to your <head> to prevent flash of wrong theme:');
        $this->line('  @include("darkmode::init-script")');

        return self::SUCCESS;
    }
}
