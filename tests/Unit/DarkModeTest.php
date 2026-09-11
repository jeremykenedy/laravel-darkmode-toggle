<?php

use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\Profile;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\User;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\UserWithoutProfile;

it('inherits the css framework from ui-kit when the package setting is null', function () {
    config(['darkmode.css_framework' => null, 'ui-kit.css_framework' => 'bootstrap5']);

    expect(DarkMode::cssFramework())->toBe('bootstrap5');
});

it('prefers its own css framework setting over ui-kit', function () {
    config(['darkmode.css_framework' => 'bootstrap4', 'ui-kit.css_framework' => 'bootstrap5']);

    expect(DarkMode::cssFramework())->toBe('bootstrap4');
});

it('falls back to tailwind when the configured css framework is not shipped', function () {
    config(['darkmode.css_framework' => 'material']);

    expect(DarkMode::cssFramework())->toBe('tailwind');
});

it('inherits the frontend from ui-kit when the package setting is null', function () {
    config(['darkmode.frontend' => null, 'ui-kit.frontend' => 'vue']);

    expect(DarkMode::frontend())->toBe('vue');
});

it('falls back to blade when the configured frontend is not shipped', function () {
    config(['darkmode.frontend' => 'angular']);

    expect(DarkMode::frontend())->toBe('blade');
});

it('validates the css frameworks it ships', function () {
    expect(DarkMode::CSS_FRAMEWORKS)->toBe(['tailwind', 'bootstrap5', 'bootstrap4'])
        ->and(DarkMode::isValidCssFramework('tailwind'))->toBeTrue()
        ->and(DarkMode::isValidCssFramework('bulma'))->toBeFalse()
        ->and(DarkMode::isValidCssFramework(null))->toBeFalse();
});

it('validates the frontends it ships', function () {
    expect(DarkMode::FRONTENDS)->toBe(['blade', 'livewire', 'vue', 'react', 'svelte'])
        ->and(DarkMode::isValidFrontend('svelte'))->toBeTrue()
        ->and(DarkMode::isValidFrontend('ember'))->toBeFalse()
        ->and(DarkMode::isValidFrontend(null))->toBeFalse();
});

it('has a label for every framework it ships', function () {
    expect(array_keys(DarkMode::CSS_LABELS))->toBe(DarkMode::CSS_FRAMEWORKS)
        ->and(array_keys(DarkMode::FRONTEND_LABELS))->toBe(DarkMode::FRONTENDS);
});

it('falls back to sane values when string settings are emptied out', function () {
    config([
        'darkmode.prefix'        => '',
        'darkmode.class_name'    => null,
        'darkmode.storage_key'   => '',
        'darkmode.persist_field' => null,
    ]);

    expect(DarkMode::prefix())->toBe('darkmode')
        ->and(DarkMode::className())->toBe('dark')
        ->and(DarkMode::storageKey())->toBe('theme')
        ->and(DarkMode::persistField())->toBe('dark_mode');
});

it('treats an unset or empty data attribute as off', function () {
    config(['darkmode.data_attribute' => null]);
    expect(DarkMode::dataAttribute())->toBeNull();

    config(['darkmode.data_attribute' => '']);
    expect(DarkMode::dataAttribute())->toBeNull();

    config(['darkmode.data_attribute' => 'data-bs-theme']);
    expect(DarkMode::dataAttribute())->toBe('data-bs-theme');
});

it('reads the stored preference off the user profile', function () {
    $profile = new Profile();
    $profile->dark_mode = 'dark';

    expect(DarkMode::preferenceFor(new User($profile)))->toBe('dark');
});

it('reads the preference from the configured field', function () {
    config(['darkmode.persist_field' => 'theme_choice']);

    $profile = new Profile();
    $profile->theme_choice = 'light';
    $profile->dark_mode = 'dark';

    expect(DarkMode::preferenceFor(new User($profile)))->toBe('light');
});

it('ignores a stored preference that is not a supported mode', function () {
    $profile = new Profile();
    $profile->dark_mode = 'sepia';

    expect(DarkMode::preferenceFor(new User($profile)))->toBeNull();
});

it('returns null when there is no user, no profile or no stored value', function () {
    expect(DarkMode::preferenceFor(null))->toBeNull()
        ->and(DarkMode::preferenceFor(new User()))->toBeNull()
        ->and(DarkMode::preferenceFor(new UserWithoutProfile()))->toBeNull()
        ->and(DarkMode::preferenceFor(new User(new Profile())))->toBeNull();
});

it('writes the preference to the user profile', function () {
    $user = new User();

    DarkMode::persistFor($user, 'dark');

    expect($user->profile)->not->toBeNull()
        ->and($user->profile->dark_mode)->toBe('dark');
});

it('writes the preference to the configured field', function () {
    config(['darkmode.persist_field' => 'theme_choice']);

    $user = new User();

    DarkMode::persistFor($user, 'light');

    expect($user->profile->theme_choice)->toBe('light')
        ->and($user->profile->dark_mode)->toBeNull();
});

it('never writes an unsupported mode', function () {
    $user = new User(new Profile());

    DarkMode::persistFor($user, 'sepia');

    expect($user->profile->dark_mode)->toBeNull();
});

it('leaves applications that do not use the profile convention alone', function () {
    $user = new UserWithoutProfile();

    DarkMode::persistFor($user, 'dark');

    expect($user->profile ?? null)->toBeNull();
});

it('does nothing when there is no authenticated user', function () {
    DarkMode::persistFor(null, 'dark');
})->throwsNoExceptions();

it('falls back to system when the configured default is not a supported mode', function () {
    config(['darkmode.default' => 'sepia']);

    expect(DarkMode::defaultMode())->toBe('system');
});

it('uses the configured default when it is a supported mode', function (string $mode) {
    config(['darkmode.default' => $mode]);

    expect(DarkMode::defaultMode())->toBe($mode);
})->with(['light', 'dark', 'system']);
