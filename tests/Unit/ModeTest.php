<?php

use Jeremykenedy\LaravelDarkmodeToggle\Enums\Mode;

it('exposes exactly the three supported modes in display order', function () {
    expect(Mode::values())->toBe(['light', 'dark', 'system']);
});

it('accepts every supported mode', function (string $mode) {
    expect(Mode::isValid($mode))->toBeTrue();
})->with(['light', 'dark', 'system']);

it('rejects anything that is not a supported mode', function (mixed $value) {
    expect(Mode::isValid($value))->toBeFalse();
})->with([
    'unknown string' => 'auto',
    'empty string'   => '',
    'wrong case'     => 'Dark',
    'null'           => null,
    'integer'        => 1,
    'array'          => [['dark']],
    'boolean'        => true,
]);
