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
        {--frontend= : Frontend framework (blade, livewire, vue, react, svelte)}
        {--force : Skip confirmation when reinstalling}';

    protected $description = 'Install and configure the Laravel Dark Mode Toggle package';

    public function handle(): int
    {
        $this->renderBanner('DARKMODE');

        if ($this->isAlreadyInstalled() && !$this->option('force')) {
            $this->warn('  Dark Mode Toggle is already installed.');
            $this->newLine();
            $this->line('  To change frameworks, use the update command instead:');
            $this->line('    <comment>php artisan darkmode:update</comment>');
            $this->newLine();
            $this->line('  To switch a single setting quickly:');
            $this->line('    <comment>php artisan darkmode:switch --css=bootstrap5</comment>');
            $this->newLine();
            $this->warn('  Reinstalling will overwrite your config and published views.');
            $this->warn('  This is a destructive action that resets all package settings.');
            $this->newLine();

            if ($this->option('no-interaction')) {
                $this->error('  Already installed. Use --force to reinstall non-interactively.');

                return self::FAILURE;
            }

            $confirm = $this->ask('  Type "yes" to reinstall from scratch, or any other key to cancel');

            if ($confirm !== 'yes') {
                $this->info('  Cancelled. No changes were made.');

                return self::SUCCESS;
            }

            $this->newLine();
        }

        $result = $this->promptFrameworks();
        if ($result === false) {
            return self::FAILURE;
        }

        $this->call('vendor:publish', ['--tag' => 'darkmode-config', '--force' => true]);

        $this->setCssFramework($result['css']);
        $this->setFrontendFramework($result['frontend']);

        $this->showSummary('Laravel Dark Mode Toggle', $result['css'], $result['frontend']);

        return self::SUCCESS;
    }

    protected function isAlreadyInstalled(): bool
    {
        return file_exists(config_path('darkmode.php'));
    }
}
