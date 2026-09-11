<?php

use Jeremykenedy\LaravelDarkmodeToggle\Livewire\DarkmodeToggle;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\Profile;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\User;
use Livewire\Livewire;

it('mounts on the configured default for guests', function () {
    config(['darkmode.default' => 'light']);

    Livewire::test(DarkmodeToggle::class)->assertSet('current', 'light');
});

it('mounts on the stored preference for a signed in user', function () {
    $profile = new Profile();
    $profile->dark_mode = 'dark';

    $this->actingAs(new User($profile));

    Livewire::test(DarkmodeToggle::class)->assertSet('current', 'dark');
});

it('changes the active mode', function (string $mode) {
    Livewire::test(DarkmodeToggle::class)
        ->call('setTheme', $mode)
        ->assertSet('current', $mode);
})->with(['light', 'dark', 'system']);

it('ignores a mode it does not support', function () {
    Livewire::test(DarkmodeToggle::class)
        ->call('setTheme', 'sepia')
        ->assertSet('current', 'system');
});

it('tells the browser about the change', function () {
    Livewire::test(DarkmodeToggle::class)
        ->call('setTheme', 'dark')
        ->assertDispatched('theme-changed', mode: 'dark');
});

it('writes the choice to the user profile', function () {
    $user = new User();

    $this->actingAs($user);

    Livewire::test(DarkmodeToggle::class)->call('setTheme', 'dark');

    expect($user->profile->dark_mode)->toBe('dark');
});

it('renders the framework specific markup', function (string $css, string $expected, string $notExpected) {
    $this->useCssFramework($css);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain($expected)
        ->and($html)->not->toContain($notExpected);
})->with([
    ['tailwind', 'rounded-lg bg-white', 'dropdown-item'],
    ['bootstrap5', 'dropdown-menu dropdown-menu-end', 'rounded-lg bg-white'],
    ['bootstrap4', 'dropdown-menu show', 'dropdown-menu-end'],
]);

it('exposes the livewire menu to assistive technology', function (string $css) {
    $this->useCssFramework($css);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain('aria-haspopup="true"')
        ->and($html)->toContain('role="menu"')
        ->and(substr_count($html, 'role="menuitemradio"'))->toBe(3)
        ->and($html)->toContain('aria-checked="false"')
        ->and($html)->toContain('aria-checked="true"');
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);

it('wires every option to the server', function (string $css) {
    $this->useCssFramework($css);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain('wire:click="setTheme(\'light\')"')
        ->and($html)->toContain('wire:click="setTheme(\'dark\')"')
        ->and($html)->toContain('wire:click="setTheme(\'system\')"');
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);

it('applies the theme in the browser without waiting for a round trip', function (string $css) {
    $this->useCssFramework($css);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain('classList.toggle')
        ->and($html)->toContain('theme-changed.window');
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);

it('ignores a configured default that is not a supported mode', function () {
    config(['darkmode.default' => 'sepia']);

    Livewire::test(DarkmodeToggle::class)->assertSet('current', 'system');
});

it('does not write to the profile when server persistence is switched off', function () {
    config(['darkmode.persist_to_server' => false]);

    $user = new User();

    $this->actingAs($user);

    Livewire::test(DarkmodeToggle::class)->call('setTheme', 'dark')->assertSet('current', 'dark');

    expect($user->profile)->toBeNull();
});

it('still applies the theme in the browser when server persistence is off', function () {
    config(['darkmode.persist_to_server' => false]);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain('classList.toggle');
});

it('does not listen for storage events until cross tab sync is switched on', function (string $css) {
    $this->useCssFramework($css);

    expect(Livewire::test(DarkmodeToggle::class)->html())->not->toContain("addEventListener('storage'");
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);

it('keeps other tabs in step when cross tab sync is switched on', function (string $css) {
    $this->useCssFramework($css, ['darkmode.sync_across_tabs' => true]);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain("window.addEventListener('storage'")
        ->and($html)->toContain("event.key === 'theme'");
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);

it('normalises settings so it cannot emit an empty class token', function () {
    config(['darkmode.class_name' => '', 'darkmode.storage_key' => '']);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    expect($html)->toContain("classList.toggle('dark'")
        ->and($html)->not->toContain("classList.toggle(''")
        ->and($html)->toContain("localStorage.setItem('theme'");
});

it('keeps the alpine attribute intact rather than closing it early', function (string $css) {
    $this->useCssFramework($css, ['darkmode.sync_across_tabs' => true]);

    $html = Livewire::test(DarkmodeToggle::class)->html();

    preg_match('/x-data="([^"]*)"/s', $html, $matches);

    $attribute = $matches[1] ?? '';

    expect($attribute)->toContain('apply(')
        ->and($attribute)->toContain('move(')
        ->and($attribute)->toContain("['light', 'dark', 'system']")
        ->and(substr_count($attribute, '"'))->toBe(0);
})->with(['tailwind', 'bootstrap5', 'bootstrap4']);
