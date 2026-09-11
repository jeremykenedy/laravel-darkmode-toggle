<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Console;

use Illuminate\Console\Command;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HandlesFrameworkSetup;

use function Laravel\Prompts\info;

class SwitchCommand extends Command
{
    use HandlesFrameworkSetup;

    protected $signature = 'darkmode:switch
        {--css= : CSS framework (tailwind, bootstrap5, bootstrap4)}
        {--frontend= : Frontend framework (blade, livewire, vue, react, svelte)}';

    protected $description = 'Switch the CSS and/or frontend framework for Laravel Dark Mode Toggle';

    public function handle(): int
    {
        $css = $this->option('css');
        $frontend = $this->option('frontend');

        if (!$css && !$frontend) {
            $this->error('Provide at least one of --css or --frontend.');
            $this->line('');
            $this->line('  Examples:');
            $this->line('    php artisan darkmode:switch --css=bootstrap5');
            $this->line('    php artisan darkmode:switch --frontend=livewire');
            $this->line('    php artisan darkmode:switch --css=tailwind --frontend=vue');

            return self::FAILURE;
        }

        if ($css && !$this->validateCssFramework($css)) {
            return self::FAILURE;
        }

        if ($frontend && !$this->validateFrontend($frontend)) {
            return self::FAILURE;
        }

        if ($css) {
            $this->setCssFramework($css);
            info("Dark mode CSS framework switched to: {$css}");
        }

        if ($frontend) {
            $this->setFrontendFramework($frontend);
            info("Dark mode frontend framework switched to: {$frontend}");
        }

        info('Run: php artisan view:clear && npm run build');

        return self::SUCCESS;
    }
}
