<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Console\InstallCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Console\SwitchCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Console\UpdateCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle;
use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;
use Livewire\Livewire;

class DarkmodeToggleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/darkmode.php', 'darkmode');
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerTranslations();
        $this->registerRoutes();
        $this->registerComponents();
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register the view namespace for the active CSS framework.
     *
     * Lookup order is the framework Blade views, then anything else that
     * framework ships such as its Livewire markup, then the shared views.
     */
    protected function registerViews(): void
    {
        $base = __DIR__.'/../resources/views';
        $css = DarkMode::cssFramework();

        if (!is_dir($base.'/'.$css)) {
            $css = 'tailwind';
        }

        $prefix = DarkMode::prefix();

        $this->loadViewsFrom([$base.'/'.$css.'/blade', $base.'/'.$css, $base], $prefix);

        $livewirePath = $base.'/livewire';

        if (is_dir($livewirePath)) {
            $this->loadViewsFrom($livewirePath, $prefix.'-livewire');
        }
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'darkmode');
    }

    protected function registerRoutes(): void
    {
        if (config('darkmode.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        }
    }

    protected function registerComponents(): void
    {
        Blade::component(Toggle::class, 'darkmode-toggle');

        if (class_exists(Livewire::class)) {
            Livewire::component('darkmode-toggle', DarkmodeToggle::class);
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                UpdateCommand::class,
                SwitchCommand::class,
            ]);
        }
    }

    protected function registerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../../config/darkmode.php' => config_path('darkmode.php'),
        ], 'darkmode-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/darkmode'),
        ], 'darkmode-views');

        $this->publishes([
            __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/darkmode'),
        ], 'darkmode-lang');

        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/darkmode-toggle'),
        ], 'darkmode-js');
    }
}
