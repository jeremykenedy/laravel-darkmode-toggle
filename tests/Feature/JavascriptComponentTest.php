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
