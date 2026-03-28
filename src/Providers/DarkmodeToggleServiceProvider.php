<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DarkmodeToggleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/darkmode.php', 'darkmode');

        $css = config('darkmode.css_framework') ?? config('ui-kit.css_framework', 'tailwind');
        $viewPath = __DIR__.'/../resources/views/'.$css.'/blade';

        if (!is_dir($viewPath)) {
            $viewPath = __DIR__.'/../resources/views/tailwind/blade';
        }

        $this->loadViewsFrom([$viewPath, __DIR__.'/../resources/views/'], config('darkmode.prefix', 'darkmode'));
    }

    public function boot(): void
    {
        if (config('darkmode.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        }

        Blade::component('darkmode-toggle', \Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle::class);

        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('darkmode-toggle', \Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle::class);
        }

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->commands([
                \Jeremykenedy\LaravelDarkmodeToggle\Console\InstallCommand::class,
            ]);
        }
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../../config/darkmode.php' => config_path('darkmode.php'),
        ], 'darkmode-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/darkmode'),
        ], 'darkmode-views');

        $frontend = config('ui-kit.frontend', 'blade');
        if (!in_array($frontend, ['blade', 'livewire'])) {
            $jsPath = __DIR__.'/../resources/js/'.$frontend;
            if (is_dir($jsPath)) {
                $this->publishes([
                    $jsPath => resource_path('js/vendor/darkmode-toggle'),
                ], 'darkmode-'.$frontend);
            }
        }
    }
}
