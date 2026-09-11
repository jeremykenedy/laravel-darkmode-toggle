<?php

use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;
use Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns\HandlesFrameworkSetup;

it('switches the css framework', function (string $css) {
    $this->artisan('darkmode:switch', ['--css' => $css])
        ->expectsOutputToContain("Dark mode CSS framework switched to: {$css}")
        ->assertSuccessful();
})->with(DarkMode::CSS_FRAMEWORKS);

it('switches the frontend', function (string $frontend) {
    $this->artisan('darkmode:switch', ['--frontend' => $frontend])
        ->expectsOutputToContain("Dark mode frontend framework switched to: {$frontend}")
        ->assertSuccessful();
})->with(DarkMode::FRONTENDS);

it('switches every combination at once', function (string $css, string $frontend) {
    $this->artisan('darkmode:switch', ['--css' => $css, '--frontend' => $frontend])->assertSuccessful();
})->with(DarkMode::CSS_FRAMEWORKS)->with(DarkMode::FRONTENDS);

it('needs at least one framework to switch', function () {
    $this->artisan('darkmode:switch')
        ->expectsOutputToContain('Provide at least one of --css or --frontend')
        ->assertFailed();
});

it('shows examples when called with nothing', function () {
    $this->artisan('darkmode:switch')
        ->expectsOutputToContain('php artisan darkmode:switch --css=bootstrap5')
        ->assertFailed();
});

it('refuses a css framework it does not ship', function () {
    $this->artisan('darkmode:switch', ['--css' => 'material'])
        ->expectsOutputToContain('Invalid CSS framework: material')
        ->assertFailed();
});

it('refuses a frontend it does not ship', function () {
    $this->artisan('darkmode:switch', ['--frontend' => 'angular'])
        ->expectsOutputToContain('Invalid frontend: angular')
        ->assertFailed();
});

it('runs without needing the package to be installed first', function () {
    @unlink(config_path('darkmode.php'));

    $this->artisan('darkmode:switch', ['--css' => 'tailwind'])->assertSuccessful();
});

it('keeps the package env keys in step so a switch cannot silently do nothing', function () {
    $trait = new ReflectionClass(HandlesFrameworkSetup::class);

    $css = $trait->getMethod('setCssFramework')->getFileName();
    $source = file_get_contents($css);

    expect($source)->toContain("updateEnvValueIfPresent('DARKMODE_CSS'")
        ->and($source)->toContain("updateEnvValueIfPresent('DARKMODE_FRONTEND'");
});
