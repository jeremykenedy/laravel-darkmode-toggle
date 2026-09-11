<?php

use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;

beforeEach(function () {
    @mkdir(config_path(), 0755, true);
    file_put_contents(config_path('darkmode.php'), '<?php return [];');
});

afterEach(fn () => @unlink(config_path('darkmode.php')));

it('refuses to run before the package is installed', function () {
    @unlink(config_path('darkmode.php'));

    $this->artisan('darkmode:update', ['--css' => 'bootstrap5'])
        ->expectsOutputToContain('not installed yet')
        ->expectsOutputToContain('php artisan darkmode:install')
        ->assertFailed();
});

it('updates the css framework on its own', function (string $css) {
    $this->artisan('darkmode:update', ['--css' => $css])
        ->expectsOutputToContain("CSS framework updated to: {$css}")
        ->assertSuccessful();
})->with(DarkMode::CSS_FRAMEWORKS);

it('updates the frontend on its own', function (string $frontend) {
    $this->artisan('darkmode:update', ['--frontend' => $frontend])
        ->expectsOutputToContain("Frontend framework updated to: {$frontend}")
        ->assertSuccessful();
})->with(DarkMode::FRONTENDS);

it('updates every combination at once', function (string $css, string $frontend) {
    $this->artisan('darkmode:update', ['--css' => $css, '--frontend' => $frontend])->assertSuccessful();
})->with(DarkMode::CSS_FRAMEWORKS)->with(DarkMode::FRONTENDS);

it('refuses a css framework it does not ship', function () {
    $this->artisan('darkmode:update', ['--css' => 'material'])
        ->expectsOutputToContain('Invalid CSS framework: material')
        ->assertFailed();
});

it('refuses a frontend it does not ship', function () {
    $this->artisan('darkmode:update', ['--frontend' => 'angular'])
        ->expectsOutputToContain('Invalid frontend: angular')
        ->assertFailed();
});

it('leaves the published config in place', function () {
    $this->artisan('darkmode:update', ['--css' => 'bootstrap5'])->assertSuccessful();

    expect(file_get_contents(config_path('darkmode.php')))->toBe('<?php return [];');
});
