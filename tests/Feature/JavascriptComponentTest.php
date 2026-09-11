<?php

function componentSource(string $file): string
{
    return file_get_contents(__DIR__.'/../../src/resources/js/'.$file);
}

dataset('javascript components', [
    'vue'    => 'vue/DarkmodeToggle.vue',
    'react'  => 'react/DarkmodeToggle.jsx',
    'svelte' => 'svelte/DarkmodeToggle.svelte',
]);

it('ships a component for every javascript frontend', function (string $file) {
    expect(file_exists(__DIR__.'/../../src/resources/js/'.$file))->toBeTrue();
})->with('javascript components');

it('accepts every documented prop', function (string $file) {
    $source = componentSource($file);

    foreach (['defaultMode', 'storageKey', 'persistUrl', 'persistMethod', 'persistField', 'className'] as $prop) {
        expect($source)->toContain($prop);
    }
})->with('javascript components');

it('matches the blade feature set', function (string $file) {
    $source = componentSource($file);

    expect($source)->toContain('dataAttribute')
        ->and($source)->toContain('colorScheme')
        ->and($source)->toContain('toggleLabel')
        ->and($source)->toContain('labels');
})->with('javascript components');

it('follows the operating system while the system mode is active', function (string $file) {
    expect(componentSource($file))->toContain("matchMedia('(prefers-color-scheme: dark)')")
        ->and(componentSource($file))->toContain("addEventListener('change'");
})->with('javascript components');

it('removes the listeners it adds', function (string $file) {
    expect(componentSource($file))->toContain("removeEventListener('change'");
})->with('javascript components');

it('survives local storage being unavailable', function (string $file) {
    expect(componentSource($file))->toContain('try {')
        ->and(componentSource($file))->toContain('catch (error)');
})->with('javascript components');

it('exposes the menu to assistive technology', function (string $file) {
    $source = componentSource($file);

    expect($source)->toContain('aria-haspopup')
        ->and($source)->toContain('role="menu"')
        ->and($source)->toContain('menuitemradio');
})->with('javascript components');

it('closes itself without relying on a directive the host app has to register', function () {
    expect(componentSource('vue/DarkmodeToggle.vue'))
        ->not->toContain('v-click-outside')
        ->and(componentSource('vue/DarkmodeToggle.vue'))->toContain('onDocumentClick');
});

it('sends the preference to the server when a url is given', function (string $file) {
    expect(componentSource($file))->toContain('X-CSRF-TOKEN')
        ->and(componentSource($file))->toContain("credentials: 'same-origin'");
})->with('javascript components');

it('supports the keyboard navigation the readme documents', function (string $file) {
    $source = componentSource($file);

    // Each framework spells the key its own way, for example arrow-down against ArrowDown.
    $keys = str_replace('-', '', strtolower($source));

    expect($keys)->toContain('arrowdown')
        ->and($keys)->toContain('arrowup')
        ->and($keys)->toContain('escape')
        ->and($source)->toContain('menuitemradio')
        ->and($source)->toContain('focus()');
})->with('javascript components');

it('tracks the selected mode in the system change listener rather than re-reading storage', function () {
    $source = componentSource('react/DarkmodeToggle.jsx');

    expect($source)->toContain('currentRef.current')
        ->and($source)->toContain("if (currentRef.current === 'system')");
});

it('supports cross tab sync with cleanup', function (string $file) {
    $source = componentSource($file);

    expect($source)->toContain('syncAcrossTabs')
        ->and($source)->toContain("addEventListener('storage'")
        ->and($source)->toContain("removeEventListener('storage'");
})->with('javascript components');

it('ignores stored values that are not supported modes', function (string $file) {
    $source = componentSource($file);

    expect($source)->toContain('modes.includes(value)')
        ->and($source)->toContain('modes.includes(event.newValue)');
})->with('javascript components');
