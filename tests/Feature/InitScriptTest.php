<?php

function initScript(): string
{
    return view('darkmode::init-script')->render();
}

it('init script prevents flash of wrong theme', function () {
    $html = initScript();

    expect($html)->toContain('localStorage')
        ->and($html)->toContain('classList')
        ->and($html)->toContain('<script>')
        ->and($html)->toContain('[x-cloak]');
});

it('init script uses configured storage key', function () {
    config(['darkmode.storage_key' => 'my-theme']);

    expect(initScript())->toContain('my-theme');
});

it('init script uses configured class name', function () {
    config(['darkmode.class_name' => 'dark-mode']);

    expect(initScript())->toContain('dark-mode');
});

it('falls back to the configured default when nothing is stored', function () {
    config(['darkmode.default' => 'dark']);

    expect(initScript())->toContain('var fallback = "dark"');
});

it('survives local storage being unavailable', function () {
    expect(initScript())->toContain('try {')
        ->and(initScript())->toContain('catch (e)');
});

it('keeps the system mode honest before the frontend boots', function () {
    expect(initScript())->toContain("matchMedia('(prefers-color-scheme: dark)')")
        ->and(initScript())->toContain('addEventListener');
});

it('leaves the data attribute and color scheme alone by default', function () {
    $html = initScript();

    expect($html)->toContain('var attr = null')
        ->and($html)->toContain('var colorScheme = false');
});

it('mirrors the theme onto the data attribute when one is configured', function () {
    config(['darkmode.data_attribute' => 'data-bs-theme']);

    expect(initScript())->toContain('var attr = "data-bs-theme"');
});

it('mirrors the theme onto the color scheme property when switched on', function () {
    config(['darkmode.color_scheme' => true]);

    expect(initScript())->toContain('var colorScheme = true')
        ->and(initScript())->toContain('root.style.colorScheme');
});
