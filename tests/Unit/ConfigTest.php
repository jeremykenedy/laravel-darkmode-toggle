<?php

it('registers config defaults', function () {
    expect(config('darkmode.strategy'))->toBe('class')
        ->and(config('darkmode.class_name'))->toBe('dark')
        ->and(config('darkmode.default'))->toBe('system')
        ->and(config('darkmode.storage_key'))->toBe('theme')
        ->and(config('darkmode.persist_to_server'))->toBeTrue()
        ->and(config('darkmode.persist_route'))->toBe('/profile/dark-mode')
        ->and(config('darkmode.persist_method'))->toBe('PUT')
        ->and(config('darkmode.persist_field'))->toBe('dark_mode')
        ->and(config('darkmode.prefix'))->toBe('darkmode');
});

it('has valid default mode', function () {
    expect(config('darkmode.default'))->toBeIn(['light', 'dark', 'system']);
});

it('has valid strategy', function () {
    expect(config('darkmode.strategy'))->toBe('class');
});

it('has route configuration', function () {
    expect(config('darkmode.routes'))->toBeArray()
        ->and(config('darkmode.routes'))->toHaveKeys(['enabled', 'prefix', 'middleware']);
});

it('css_framework defaults to null for ui-kit inheritance', function () {
    expect(config('darkmode.css_framework'))->toBeNull();
});

it('ships the same defaults the readme documents', function () {
    $shipped = require __DIR__.'/../../config/darkmode.php';

    expect($shipped['strategy'])->toBe('class')
        ->and($shipped['class_name'])->toBe('dark')
        ->and($shipped['default'])->toBe('system')
        ->and($shipped['storage_key'])->toBe('theme')
        ->and($shipped['persist_to_server'])->toBeTrue()
        ->and($shipped['persist_route'])->toBe('/profile/dark-mode')
        ->and($shipped['persist_method'])->toBe('PUT')
        ->and($shipped['persist_field'])->toBe('dark_mode')
        ->and($shipped['prefix'])->toBe('darkmode')
        ->and($shipped['css_framework'])->toBeNull()
        ->and($shipped['frontend'])->toBeNull()
        ->and($shipped['routes']['enabled'])->toBeTrue()
        ->and($shipped['routes']['prefix'])->toBe('darkmode')
        ->and($shipped['routes']['middleware'])->toBe(['web', 'auth']);
});

it('ships the additive settings switched off so existing installs are unchanged', function () {
    $shipped = require __DIR__.'/../../config/darkmode.php';

    expect($shipped['data_attribute'])->toBeNull()
        ->and($shipped['color_scheme'])->toBeFalse()
        ->and($shipped['sync_across_tabs'])->toBeFalse();
});
