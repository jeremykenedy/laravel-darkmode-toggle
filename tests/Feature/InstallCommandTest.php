<?php

use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;

beforeEach(fn () => @unlink(config_path('darkmode.php')));

afterEach(fn () => @unlink(config_path('darkmode.php')));

it('publishes the config on a fresh install', function () {
    expect(file_exists(config_path('darkmode.php')))->toBeFalse();

    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])->assertSuccessful();

    expect(file_exists(config_path('darkmode.php')))->toBeTrue();
});

it('installs every css and frontend combination', function (string $css, string $frontend) {
    $this->artisan('darkmode:install', [
        '--css'            => $css,
        '--frontend'       => $frontend,
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertSuccessful();
})->with(DarkMode::CSS_FRAMEWORKS)->with(DarkMode::FRONTENDS);

it('refuses a css framework it does not ship', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'material',
        '--frontend'       => 'blade',
        '--force'          => true,
        '--no-interaction' => true,
    ])->expectsOutputToContain('Invalid CSS framework: material')->assertFailed();
});

it('refuses a frontend it does not ship', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'angular',
        '--force'          => true,
        '--no-interaction' => true,
    ])->expectsOutputToContain('Invalid frontend: angular')->assertFailed();
});

it('will not silently reinstall over an existing install', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $this->artisan('darkmode:install', [
        '--css'            => 'bootstrap5',
        '--frontend'       => 'vue',
        '--no-interaction' => true,
    ])->expectsOutputToContain('Use --force to reinstall non-interactively')->assertFailed();
});

it('points at the update command instead of overwriting', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])->expectsOutputToContain('php artisan darkmode:update')->assertFailed();
});

it('reinstalls when forced', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $this->artisan('darkmode:install', [
        '--css'            => 'bootstrap4',
        '--frontend'       => 'react',
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertSuccessful();
});

it('falls back to the configured frameworks when none are passed', function () {
    config(['ui-kit.css_framework' => 'bootstrap5', 'ui-kit.frontend' => 'livewire']);

    $this->artisan('darkmode:install', ['--no-interaction' => true])
        ->expectsOutputToContain('Bootstrap 5')
        ->expectsOutputToContain('Livewire 3')
        ->assertSuccessful();
});

it('tells the developer what to do next', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain("@include('darkmode::init-script')")
        ->expectsOutputToContain('<x-darkmode-toggle />')
        ->assertSuccessful();
});

it('refuses an unsupported css framework even when only that flag is passed', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'material',
        '--no-interaction' => true,
    ])->expectsOutputToContain('Invalid CSS framework: material')->assertFailed();

    expect(file_exists(config_path('darkmode.php')))->toBeFalse();
});

it('refuses an unsupported frontend even when only that flag is passed', function () {
    $this->artisan('darkmode:install', [
        '--frontend'       => 'angular',
        '--no-interaction' => true,
    ])->expectsOutputToContain('Invalid frontend: angular')->assertFailed();

    expect(file_exists(config_path('darkmode.php')))->toBeFalse();
});
