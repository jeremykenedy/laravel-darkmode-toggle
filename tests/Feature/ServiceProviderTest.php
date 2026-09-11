<?php

use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle;
use Jeremykenedy\LaravelDarkmodeToggle\Providers\DarkmodeToggleServiceProvider;
use Livewire\Livewire;

it('registers the darkmode-toggle blade component', function () {
    $aliases = app('blade.compiler')->getClassComponentAliases();

    expect($aliases)->toHaveKey('darkmode-toggle')
        ->and($aliases['darkmode-toggle'])->toBe(Toggle::class);
});

it('registers the darkmode-toggle livewire component', function () {
    expect(Livewire::new('darkmode-toggle'))->toBeInstanceOf(DarkmodeToggle::class);
});

it('resolves the toggle view through the configured prefix', function () {
    expect(view()->exists('darkmode::toggle'))->toBeTrue()
        ->and(view()->exists('darkmode::init-script'))->toBeTrue()
        ->and(view()->exists('darkmode::livewire.toggle'))->toBeTrue();
});

it('keeps the standalone livewire view namespace available', function () {
    expect(view()->exists('darkmode-livewire::toggle'))->toBeTrue();
});

it('loads the package translations', function () {
    expect(trans('darkmode::darkmode.light'))->toBe('Light')
        ->and(trans('darkmode::darkmode.dark'))->toBe('Dark')
        ->and(trans('darkmode::darkmode.system'))->toBe('System')
        ->and(trans('darkmode::darkmode.toggle_theme'))->toBe('Toggle theme');
});

it('publishes config, views, translations and javascript under their own tags', function () {
    $groups = DarkmodeToggleServiceProvider::pathsToPublish(DarkmodeToggleServiceProvider::class);

    expect($groups)->not->toBeEmpty();

    foreach (['darkmode-config', 'darkmode-views', 'darkmode-lang', 'darkmode-js'] as $tag) {
        expect(DarkmodeToggleServiceProvider::pathsToPublish(DarkmodeToggleServiceProvider::class, $tag))
            ->not->toBeEmpty("Publish tag [{$tag}] is missing");
    }
});

it('publishes the javascript components to the path the readme documents', function () {
    $paths = DarkmodeToggleServiceProvider::pathsToPublish(DarkmodeToggleServiceProvider::class, 'darkmode-js');

    expect(array_values($paths)[0])->toContain('js'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'darkmode-toggle');
});

it('does not register routes when they are disabled', function () {
    expect(app('router')->getRoutes()->getByName('darkmode.update'))->toBeNull();
});
