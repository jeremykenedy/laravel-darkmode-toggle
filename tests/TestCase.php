<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Tests;

use Jeremykenedy\LaravelDarkmodeToggle\Providers\DarkmodeToggleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [DarkmodeToggleServiceProvider::class];
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
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseIsSafe();
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
