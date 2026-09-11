<?php

use Illuminate\Contracts\View\View;
use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\Profile;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\User;

it('toggle component has expected public properties', function () {
    $component = new Toggle();

    expect($component->storageKey)->toBe('theme')
        ->and($component->defaultMode)->toBe('system')
        ->and($component->persistRoute)->toBe('/profile/dark-mode')
        ->and($component->persistMethod)->toBe('PUT')
        ->and($component->persistField)->toBe('dark_mode')
        ->and($component->persistToServer)->toBeTrue();
});

it('toggle component accepts custom default', function () {
    expect((new Toggle(default: 'dark'))->defaultMode)->toBe('dark');
});

it('toggle component accepts custom persist route', function () {
    expect((new Toggle(persistRoute: '/custom/route'))->persistRoute)->toBe('/custom/route');
});

it('toggle component accepts persist disabled', function () {
    expect((new Toggle(persistToServer: false))->persistToServer)->toBeFalse();
});

it('still accepts the original positional arguments', function () {
    $component = new Toggle('dark', '/custom/route', false);

    expect($component->defaultMode)->toBe('dark')
        ->and($component->persistRoute)->toBe('/custom/route')
        ->and($component->persistToServer)->toBeFalse();
});

it('toggle component userPreference returns default when no auth', function () {
    expect((new Toggle())->userPreference())->toBe('system');
});

it('returns the stored preference for a signed in user', function () {
    $profile = new Profile();
    $profile->dark_mode = 'dark';

    $this->actingAs(new User($profile));

    expect((new Toggle())->userPreference())->toBe('dark');
});

it('falls back to the configured default for a user with no stored preference', function () {
    config(['darkmode.default' => 'light']);

    $this->actingAs(new User(new Profile()));

    expect((new Toggle())->userPreference())->toBe('light');
});

it('reads the theme settings off the configuration', function () {
    config([
        'darkmode.class_name'       => 'night',
        'darkmode.storage_key'      => 'appearance',
        'darkmode.persist_field'    => 'theme_choice',
        'darkmode.data_attribute'   => 'data-bs-theme',
        'darkmode.color_scheme'     => true,
        'darkmode.sync_across_tabs' => true,
    ]);

    $component = new Toggle();

    expect($component->className)->toBe('night')
        ->and($component->storageKey)->toBe('appearance')
        ->and($component->persistField)->toBe('theme_choice')
        ->and($component->dataAttribute)->toBe('data-bs-theme')
        ->and($component->colorScheme)->toBeTrue()
        ->and($component->syncAcrossTabs)->toBeTrue();
});

it('leaves the additive settings off unless they are switched on', function () {
    $component = new Toggle();

    expect($component->dataAttribute)->toBeNull()
        ->and($component->colorScheme)->toBeFalse()
        ->and($component->syncAcrossTabs)->toBeFalse();
});

it('renders through the configured view prefix', function () {
    expect((new Toggle())->render())->toBeInstanceOf(View::class)
        ->and((new Toggle())->render()->name())->toBe('darkmode::toggle');
});

it('ignores a configured default that is not a supported mode', function () {
    config(['darkmode.default' => 'sepia']);

    $component = new Toggle();

    expect($component->defaultMode)->toBe('system')
        ->and($component->userPreference())->toBe('system');
});

it('ignores a default passed to the component that is not a supported mode', function () {
    expect((new Toggle(default: 'sepia'))->defaultMode)->toBe('system');
});

it('renders a toggle where one option is always the checked one', function () {
    config(['darkmode.default' => 'sepia']);

    $html = $this->renderToggleFor('tailwind');

    expect($html)->toContain("current: 'system'");
});
