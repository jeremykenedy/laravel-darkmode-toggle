<?php

use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\Profile;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\User;

dataset('css frameworks', ['tailwind', 'bootstrap5', 'bootstrap4']);

it('renders a trigger and the three theme options', function (string $css) {
    $html = $this->renderToggleFor($css);

    expect($html)->toContain("set('light')")
        ->and($html)->toContain("set('dark')")
        ->and($html)->toContain("set('system')")
        ->and(substr_count($html, 'role="menuitemradio"'))->toBe(3);
})->with('css frameworks');

it('exposes the menu to assistive technology', function (string $css) {
    $html = $this->renderToggleFor($css);

    expect($html)->toContain(':aria-expanded="open"')
        ->and($html)->toContain('aria-haspopup="true"')
        ->and($html)->toContain('role="menu"')
        ->and($html)->toContain(':aria-checked="current === \'light\'"')
        ->and($html)->toContain('aria-label="Toggle theme"')
        ->and($html)->toContain('aria-hidden="true"');
})->with('css frameworks');

it('can be driven from the keyboard', function (string $css) {
    $html = $this->renderToggleFor($css);

    expect($html)->toContain('keydown.escape')
        ->and($html)->toContain('keydown.arrow-down')
        ->and($html)->toContain('keydown.arrow-up');
})->with('css frameworks');

it('uses the translation file rather than hardcoded english', function (string $css) {
    app('translator')->addLines([
        'darkmode.light'        => 'Claro',
        'darkmode.dark'         => 'Oscuro',
        'darkmode.system'       => 'Sistema',
        'darkmode.toggle_theme' => 'Cambiar tema',
    ], 'es', 'darkmode');

    app()->setLocale('es');

    $html = $this->renderToggleFor($css);

    expect($html)->toContain('Claro')
        ->and($html)->toContain('Oscuro')
        ->and($html)->toContain('Sistema')
        ->and($html)->toContain('Cambiar tema');
})->with('css frameworks');

it('persists to local storage and survives storage being unavailable', function (string $css) {
    $html = $this->renderToggleFor($css);

    expect($html)->toContain('localStorage.getItem')
        ->and($html)->toContain('localStorage.setItem')
        ->and($html)->toContain('try {')
        ->and($html)->toContain('catch (error)');
})->with('css frameworks');

it('follows the operating system while the system mode is active', function (string $css) {
    $html = $this->renderToggleFor($css);

    expect($html)->toContain("window.matchMedia('(prefers-color-scheme: dark)')")
        ->and($html)->toContain("addEventListener('change'");
})->with('css frameworks');

it('toggles the configured class on the document', function (string $css) {
    $html = $this->renderToggleFor($css, ['darkmode.class_name' => 'night']);

    expect($html)->toContain("document.documentElement.classList.toggle('night'");
})->with('css frameworks');

it('leaves the data attribute out until one is configured', function (string $css) {
    expect($this->renderToggleFor($css))->not->toContain('setAttribute');
})->with('css frameworks');

it('mirrors the theme onto the configured data attribute', function (string $css) {
    $html = $this->renderToggleFor($css, ['darkmode.data_attribute' => 'data-bs-theme']);

    expect($html)->toContain("setAttribute('data-bs-theme', isDark ? 'dark' : 'light')");
})->with('css frameworks');

it('leaves the color scheme property alone until it is switched on', function (string $css) {
    expect($this->renderToggleFor($css))->not->toContain('colorScheme');
})->with('css frameworks');

it('mirrors the theme onto the color scheme property when switched on', function (string $css) {
    $html = $this->renderToggleFor($css, ['darkmode.color_scheme' => true]);

    expect($html)->toContain("style.colorScheme = isDark ? 'dark' : 'light'");
})->with('css frameworks');

it('does not listen for storage events until cross tab sync is switched on', function (string $css) {
    expect($this->renderToggleFor($css))->not->toContain("addEventListener('storage'");
})->with('css frameworks');

it('listens for storage events when cross tab sync is switched on', function (string $css) {
    $html = $this->renderToggleFor($css, ['darkmode.sync_across_tabs' => true]);

    expect($html)->toContain("window.addEventListener('storage'")
        ->and($html)->toContain("event.key === 'theme'");
})->with('css frameworks');

it('does not post to the server for guests', function (string $css) {
    expect($this->renderToggleFor($css))->not->toContain('fetch(');
})->with('css frameworks');

it('posts to the configured endpoint for signed in users', function (string $css) {
    $this->actingAs(new User(new Profile()));

    $html = $this->renderToggleFor($css, [
        'darkmode.persist_route'  => '/profile/theme',
        'darkmode.persist_method' => 'POST',
        'darkmode.persist_field'  => 'theme_choice',
    ]);

    expect($html)->toContain("fetch('/profile/theme'")
        ->and($html)->toContain("method: 'POST'")
        ->and($html)->toContain('theme_choice: mode')
        ->and($html)->toContain('X-CSRF-TOKEN');
})->with('css frameworks');

it('does not post to the server when persistence is switched off', function (string $css) {
    $this->actingAs(new User(new Profile()));

    expect($this->renderToggleFor($css, ['darkmode.persist_to_server' => false]))->not->toContain('fetch(');
})->with('css frameworks');

it('renders tailwind markup without bootstrap classes', function () {
    $html = $this->renderToggleFor('tailwind');

    expect($html)->toContain('rounded-lg bg-white')
        ->and($html)->not->toContain('dropdown-menu')
        ->and($html)->not->toContain('dropdown-item');
});

it('renders bootstrap 5 dropdown markup without tailwind panel classes', function () {
    $html = $this->renderToggleFor('bootstrap5');

    expect($html)->toContain('dropdown-menu dropdown-menu-end')
        ->and($html)->toContain('dropdown-item')
        ->and($html)->not->toContain('rounded-lg bg-white');
});

it('renders bootstrap 4 dropdown markup without the bootstrap 5 alignment class', function () {
    $html = $this->renderToggleFor('bootstrap4');

    expect($html)->toContain('dropdown-menu show')
        ->and($html)->toContain('dropdown-item')
        ->and($html)->not->toContain('dropdown-menu-end')
        ->and($html)->not->toContain('rounded-lg bg-white');
});

it('merges attributes passed to the component onto the wrapper', function () {
    config(['ui-kit.css_framework' => 'tailwind']);

    $html = Blade::render('<x-darkmode-toggle class="ms-4" data-testid="theme" />');

    expect($html)->toContain('ms-4')
        ->and($html)->toContain('data-testid="theme"')
        ->and($html)->toContain('relative');
});

it('renders through a custom view prefix', function (string $css) {
    config(['darkmode.prefix' => 'theme']);

    $base = realpath(__DIR__.'/../../src/resources/views');

    $this->app->make('view')->getFinder()->flush();
    $this->app->make('view')->replaceNamespace('theme', [$base.'/'.$css.'/blade', $base.'/'.$css, $base]);

    $html = view('theme::toggle', (new Toggle())->data())->render();

    expect($html)->toContain('x-data')
        ->and($html)->toContain("set('light')");
})->with('css frameworks');

it('renders the livewire markup through a custom view prefix', function (string $css) {
    config(['darkmode.prefix' => 'theme']);

    $base = realpath(__DIR__.'/../../src/resources/views');

    $this->app->make('view')->getFinder()->flush();
    $this->app->make('view')->replaceNamespace('theme', [$base.'/'.$css.'/blade', $base.'/'.$css, $base]);

    $html = view('theme::livewire.toggle', ['current' => 'dark'])->render();

    expect($html)->toContain('x-data')
        ->and($html)->toContain('setTheme');
})->with('css frameworks');
