<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Tests;

use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Providers\DarkmodeToggleServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            DarkmodeToggleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('database.connections.mysql', null);
        $app['config']->set('database.connections.pgsql', null);
        $app['config']->set('database.connections.sqlsrv', null);

        $app['config']->set('darkmode.strategy', 'class');
        $app['config']->set('darkmode.class_name', 'dark');
        $app['config']->set('darkmode.default', 'system');
        $app['config']->set('darkmode.storage_key', 'theme');
        $app['config']->set('darkmode.persist_to_server', true);
        $app['config']->set('darkmode.persist_route', '/profile/dark-mode');
        $app['config']->set('darkmode.persist_method', 'PUT');
        $app['config']->set('darkmode.persist_field', 'dark_mode');
        $app['config']->set('darkmode.prefix', 'darkmode');
        $app['config']->set('darkmode.routes.enabled', false);
        $app['config']->set('ui-kit.css_framework', 'tailwind');
        $app['config']->set('ui-kit.frontend', 'blade');

        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.key', 'base64:'.base64_encode('darkmode-toggle-testing-key-1234'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseIsSafe();
    }

    /**
     * Point the view namespace at one CSS framework without rebooting the application.
     */
    protected function useCssFramework(string $css, array $config = []): void
    {
        config(array_merge(['ui-kit.css_framework' => $css], $config));

        $base = realpath(__DIR__.'/../src/resources/views');

        $this->app->make('view')->getFinder()->flush();
        $this->app->make('view')->replaceNamespace('darkmode', [
            $base.'/'.$css.'/blade',
            $base.'/'.$css,
            $base,
        ]);
    }

    protected function renderToggleFor(string $css, array $config = []): string
    {
        $this->useCssFramework($css, $config);

        $component = new Toggle();

        return view('darkmode::toggle', $component->data())->render();
    }

    private function assertDatabaseIsSafe(): void
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        if ($connection !== null) {
            $dbDriver = $connection['driver'] ?? 'unknown';
            $dbName = $connection['database'] ?? 'unknown';

            if ($dbDriver !== 'sqlite' || $dbName !== ':memory:') {
                $this->fail(
                    'SAFETY: Darkmode tests detected a non-memory database connection: '
                    ."{$dbDriver}/{$dbName}. Darkmode tests must NEVER touch a real database. "
                    .'Only SQLite :memory: is allowed.'
                );
            }
        }
    }
}
