<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Console;

use Illuminate\Console\Command;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HandlesFrameworkSetup;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HasInstallPrompts;

use function Laravel\Prompts\info;

class UpdateCommand extends Command
{
    use HandlesFrameworkSetup;
    use HasInstallPrompts;

    protected $signature = 'darkmode:update
        {--css= : CSS framework (tailwind, bootstrap5, bootstrap4)}
        {--frontend= : Frontend framework (blade, livewire, vue, react, svelte)}';

    protected $description = 'Update the CSS and/or frontend framework for Laravel Dark Mode Toggle';

    public function handle(): int
    {
        $this->renderBanner('DARKMODE');

        if (!$this->isInstalled()) {
            $this->warn('  Dark Mode Toggle is not installed yet.');
            $this->newLine();
            $this->line('  Run the install command first:');
            $this->line('    <comment>php artisan darkmode:install</comment>');

            return self::FAILURE;
        }

        $css = $this->option('css');
        $frontend = $this->option('frontend');

        if (!$css && !$frontend) {
            $result = $this->promptFrameworks();
            if ($result === false) {
                return self::FAILURE;
            }
            $css = $result['css'];
            $frontend = $result['frontend'];
        } else {
            $validCss = ['tailwind', 'bootstrap5', 'bootstrap4'];
            $validFrontend = ['blade', 'livewire', 'vue', 'react', 'svelte'];

            if ($css && !in_array($css, $validCss)) {
                $this->error("Invalid CSS framework: {$css}. Valid: ".implode(', ', $validCss));

                return self::FAILURE;
            }

            if ($frontend && !in_array($frontend, $validFrontend)) {
                $this->error("Invalid frontend: {$frontend}. Valid: ".implode(', ', $validFrontend));

                return self::FAILURE;
            }
        }

        if ($css) {
            $this->setCssFramework($css);
            info("CSS framework updated to: {$css}");
        }

        if ($frontend) {
            $this->setFrontendFramework($frontend);
            info("Frontend framework updated to: {$frontend}");
        }

        info('Run: php artisan view:clear && npm run build');

        return self::SUCCESS;
    }

    protected function isInstalled(): bool
    {
        return file_exists(config_path('darkmode.php'));
    }
}
