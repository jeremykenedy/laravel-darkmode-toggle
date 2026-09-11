<?php

use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\Profile;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\User;
use Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures\UserWithoutProfile;

it('registers the preference route when routes are enabled', function () {
    $route = app('router')->getRoutes()->getByName('darkmode.update');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('darkmode/preference')
        ->and($route->methods())->toContain('PUT');
});

it('registers the route under the configured prefix', function () {
    expect(app('router')->getRoutes()->getByName('darkmode.update')->uri())
        ->toStartWith(config('darkmode.routes.prefix'));
});

it('stores a valid preference for a signed in user', function (string $mode) {
    $user = new User();

    $this->actingAs($user)
        ->putJson('/darkmode/preference', ['dark_mode' => $mode])
        ->assertOk()
        ->assertJson(['dark_mode' => $mode]);

    expect($user->profile->dark_mode)->toBe($mode);
})->with(['light', 'dark', 'system']);

it('rejects a mode it does not support', function (mixed $value) {
    $this->actingAs(new User())
        ->putJson('/darkmode/preference', ['dark_mode' => $value])
        ->assertStatus(422)
        ->assertJsonValidationErrors('dark_mode');
})->with([
    'unknown mode' => 'sepia',
    'empty'        => '',
    'null'         => null,
]);

it('rejects a request with no mode at all', function () {
    $this->actingAs(new User())
        ->putJson('/darkmode/preference', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors('dark_mode');
});

it('validates the configured field name', function () {
    config(['darkmode.persist_field' => 'theme_choice']);

    $user = new User();

    $this->actingAs($user)
        ->putJson('/darkmode/preference', ['theme_choice' => 'dark'])
        ->assertOk()
        ->assertJson(['theme_choice' => 'dark']);

    expect($user->profile->theme_choice)->toBe('dark');
});

it('accepts a user whose application handles persistence itself', function () {
    $this->actingAs(new UserWithoutProfile())
        ->putJson('/darkmode/preference', ['dark_mode' => 'dark'])
        ->assertOk()
        ->assertJson(['dark_mode' => 'dark']);
});

it('overwrites a preference that was already stored', function () {
    $profile = new Profile();
    $profile->dark_mode = 'light';

    $user = new User($profile);

    $this->actingAs($user)->putJson('/darkmode/preference', ['dark_mode' => 'dark'])->assertOk();

    expect($user->profile->dark_mode)->toBe('dark');
});

it('redirects back for a standard form submission', function () {
    $this->actingAs(new User())
        ->from('/settings')
        ->put('/darkmode/preference', ['dark_mode' => 'dark'])
        ->assertRedirect('/settings');
});

it('keeps guests out', function () {
    $this->putJson('/darkmode/preference', ['dark_mode' => 'dark'])->assertUnauthorized();
});
