<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Jeremykenedy\LaravelDarkmodeToggle\Console\InstallCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Console\SwitchCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle;
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

    protected function registerViews(): void
    {
        $css = config('darkmode.css_framework') ?? config('ui-kit.css_framework', 'tailwind');
        $viewPath = __DIR__.'/../resources/views/'.$css.'/blade';

        if (!is_dir($viewPath)) {
            $viewPath = __DIR__.'/../resources/views/tailwind/blade';
        }

        $prefix = config('darkmode.prefix', 'darkmode');
        $this->loadViewsFrom([$viewPath, __DIR__.'/../resources/views/'], $prefix);

        $livewirePath = __DIR__.'/../resources/views/livewire';
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
        Blade::component('darkmode-toggle', Toggle::class);

        if (class_exists(Livewire::class)) {
            Livewire::component('darkmode-toggle', DarkmodeToggle::class);
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SwitchCommand::class,
            ]);
        }
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/darkmode.php' => config_path('darkmode.php'),
            ], 'darkmode-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/darkmode'),
            ], 'darkmode-views');

            $this->publishes([
                __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/darkmode'),
            ], 'darkmode-lang');
        }
    }
}
