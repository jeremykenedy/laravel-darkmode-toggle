<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Console;

use Illuminate\Console\Command;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HandlesFrameworkSetup;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HasInstallPrompts;

class InstallCommand extends Command
{
    use HandlesFrameworkSetup;
    use HasInstallPrompts;

    protected $signature = 'darkmode:install
        {--css= : CSS framework (tailwind, bootstrap5, bootstrap4)}
        {--frontend= : Frontend framework (blade, livewire, vue, react, svelte)}';

    protected $description = 'Install and configure the Laravel Dark Mode Toggle package';

    public function handle(): int
    {
        $this->renderBanner('DARKMODE');

        $css = $this->promptCssFramework();
        if ($css === false) {
            return self::FAILURE;
        }
        $frontend = $this->promptFrontendFramework();
        if ($frontend === false) {
            return self::FAILURE;
        }

        $this->call('vendor:publish', ['--tag' => 'darkmode-config', '--force' => true]);

        $this->setCssFramework($css);
        $this->setFrontendFramework($frontend);

        $this->showSummary('Laravel Dark Mode Toggle', $css, $frontend);

        return self::SUCCESS;
    }
}
