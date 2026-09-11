<?php

use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Console\InstallCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Console\SwitchCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Console\UpdateCommand;
use Jeremykenedy\LaravelDarkmodeToggle\Http\Controllers\DarkmodeController;
use Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle;
use Jeremykenedy\LaravelDarkmodeToggle\Providers\DarkmodeToggleServiceProvider;

/**
 * Applications already depend on the names below. Changing any of them is a
 * breaking change and has to be released as one.
 */
it('keeps the published class names', function (string $class) {
    expect(class_exists($class))->toBeTrue();
})->with([
    Toggle::class,
    DarkmodeToggle::class,
    DarkmodeController::class,
    DarkmodeToggleServiceProvider::class,
    InstallCommand::class,
    UpdateCommand::class,
    SwitchCommand::class,
]);

it('keeps the blade component alias', function () {
    expect(app('blade.compiler')->getClassComponentAliases())->toHaveKey('darkmode-toggle');
});

it('keeps the toggle component public properties', function (string $property) {
    expect(property_exists(Toggle::class, $property))->toBeTrue();
})->with(['storageKey', 'defaultMode', 'persistRoute', 'persistMethod', 'persistField', 'persistToServer']);

it('keeps the toggle component constructor signature', function () {
    $parameters = (new ReflectionMethod(Toggle::class, '__construct'))->getParameters();

    expect($parameters[0]->getName())->toBe('default')
        ->and($parameters[1]->getName())->toBe('persistRoute')
        ->and($parameters[2]->getName())->toBe('persistToServer');

    foreach ($parameters as $parameter) {
        expect($parameter->isOptional())->toBeTrue();
    }
});

it('keeps userPreference on the toggle component', function () {
    expect(method_exists(Toggle::class, 'userPreference'))->toBeTrue();
});

it('keeps the livewire component surface', function () {
    expect(property_exists(DarkmodeToggle::class, 'current'))->toBeTrue()
        ->and(method_exists(DarkmodeToggle::class, 'mount'))->toBeTrue()
        ->and(method_exists(DarkmodeToggle::class, 'setTheme'))->toBeTrue();
});

it('keeps every config key an application may already have published', function (string $key) {
    expect(config()->has("darkmode.{$key}"))->toBeTrue();
})->with([
    'strategy',
    'class_name',
    'default',
    'storage_key',
    'persist_to_server',
    'persist_route',
    'persist_method',
    'persist_field',
    'css_framework',
    'prefix',
    'routes',
]);

it('keeps the view names applications include directly', function (string $view) {
    expect(view()->exists($view))->toBeTrue();
})->with([
    'darkmode::toggle',
    'darkmode::init-script',
    'darkmode::livewire.toggle',
    'darkmode-livewire::toggle',
]);

it('keeps the publish tags', function (string $tag) {
    expect(DarkmodeToggleServiceProvider::pathsToPublish(DarkmodeToggleServiceProvider::class, $tag))->not->toBeEmpty();
})->with(['darkmode-config', 'darkmode-views', 'darkmode-lang']);

it('keeps the artisan command signatures', function (string $command) {
    expect(array_key_exists($command, app('Illuminate\Contracts\Console\Kernel')->all()))->toBeTrue();
})->with(['darkmode:install', 'darkmode:update', 'darkmode:switch']);

it('keeps the command options', function () {
    $install = app('Illuminate\Contracts\Console\Kernel')->all()['darkmode:install']->getDefinition();

    expect($install->hasOption('css'))->toBeTrue()
        ->and($install->hasOption('frontend'))->toBeTrue()
        ->and($install->hasOption('force'))->toBeTrue();
});

it('keeps rendering a toggle for an application that never changed its config', function () {
    $html = Blade::render('<x-darkmode-toggle />');

    expect($html)->toContain('x-data')
        ->and($html)->toContain("set('light')")
        ->and($html)->toContain("set('dark')")
        ->and($html)->toContain("set('system')")
        ->and($html)->toContain("localStorage.setItem('theme'")
        ->and($html)->toContain("classList.toggle('dark'");
});
