<?php

// ─── Component Class Tests ─────────────────────────────────────

use Jeremykenedy\LaravelDarkmodeToggle\Components\Toggle;

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
    $component = new Toggle(default: 'dark');

    expect($component->defaultMode)->toBe('dark');
});

it('toggle component accepts custom persist route', function () {
    $component = new Toggle(persistRoute: '/custom/route');

    expect($component->persistRoute)->toBe('/custom/route');
});

it('toggle component accepts persist disabled', function () {
    $component = new Toggle(persistToServer: false);

    expect($component->persistToServer)->toBeFalse();
});

it('toggle component userPreference returns default when no auth', function () {
    $component = new Toggle();

    expect($component->userPreference())->toBe('system');
});

// ─── Blade Component Registration ─────────────────────────────

it('registers darkmode-toggle blade component', function () {
    $aliases = app('blade.compiler')->getClassComponentAliases();

    expect($aliases)->toHaveKey('darkmode-toggle');
});

// ─── View Rendering Tests: Tailwind ────────────────────────────

it('renders tailwind toggle view', function () {
    config(['ui-kit.css_framework' => 'tailwind']);
    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('x-data')
        ->and($html)->toContain('x-cloak')
        ->and($html)->toContain('localStorage')
        ->and($html)->not->toContain('dropdown-menu');
});

it('tailwind view contains light/dark/system options', function () {
    config(['ui-kit.css_framework' => 'tailwind']);
    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain("set('light')")
        ->and($html)->toContain("set('dark')")
        ->and($html)->toContain("set('system')");
});

it('tailwind view has dark mode classes', function () {
    config(['ui-kit.css_framework' => 'tailwind']);
    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('dark:');
});

it('tailwind view has sun/moon/monitor SVG icons', function () {
    config(['ui-kit.css_framework' => 'tailwind']);
    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox');
});

it('tailwind view includes localStorage persistence', function () {
    config(['ui-kit.css_framework' => 'tailwind']);
    $component = new Toggle(persistToServer: true);
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('localStorage.setItem')
        ->and($html)->toContain('localStorage.getItem');
});

// ─── View Rendering Tests: Bootstrap 5 ─────────────────────────

it('renders bootstrap5 toggle view', function () {
    config(['ui-kit.css_framework' => 'bootstrap5']);

    $this->app->make('view')->getFinder()->flush();
    $path = realpath(__DIR__.'/../../src/resources/views/bootstrap5/blade');
    $this->app->make('view')->replaceNamespace('darkmode', [$path, realpath(__DIR__.'/../../src/resources/views/')]);

    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('dropdown')
        ->and($html)->toContain('dropdown-menu')
        ->and($html)->toContain('dropdown-item')
        ->and($html)->toContain('x-data')
        ->and($html)->not->toContain('rounded-lg bg-white');
});

it('bootstrap5 view has theme buttons', function () {
    config(['ui-kit.css_framework' => 'bootstrap5']);

    $this->app->make('view')->getFinder()->flush();
    $path = realpath(__DIR__.'/../../src/resources/views/bootstrap5/blade');
    $this->app->make('view')->replaceNamespace('darkmode', [$path, realpath(__DIR__.'/../../src/resources/views/')]);

    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain("set('light')")
        ->and($html)->toContain("set('dark')")
        ->and($html)->toContain("set('system')");
});

// ─── View Rendering Tests: Bootstrap 4 ─────────────────────────

it('renders bootstrap4 toggle view', function () {
    config(['ui-kit.css_framework' => 'bootstrap4']);

    $this->app->make('view')->getFinder()->flush();
    $path = realpath(__DIR__.'/../../src/resources/views/bootstrap4/blade');
    $this->app->make('view')->replaceNamespace('darkmode', [$path, realpath(__DIR__.'/../../src/resources/views/')]);

    $component = new Toggle();
    $html = view(config('darkmode.prefix').'::toggle', $component->data())->render();

    expect($html)->toContain('dropdown')
        ->and($html)->toContain('dropdown-item')
        ->and($html)->toContain('x-data')
        ->and($html)->not->toContain('rounded-lg bg-white');
});

// ─── Init Script Tests ─────────────────────────────────────────

it('init script prevents flash of wrong theme', function () {
    $html = view(config('darkmode.prefix').'::init-script')->render();

    expect($html)->toContain('localStorage')
        ->and($html)->toContain('classList')
        ->and($html)->toContain('<script>')
        ->and($html)->toContain('[x-cloak]');
});

it('init script uses configured storage key', function () {
    config(['darkmode.storage_key' => 'my-theme']);
    $html = view(config('darkmode.prefix').'::init-script')->render();

    expect($html)->toContain('my-theme');
});

it('init script uses configured class name', function () {
    config(['darkmode.class_name' => 'dark-mode']);
    $html = view(config('darkmode.prefix').'::init-script')->render();

    expect($html)->toContain('dark-mode');
});

// ─── Artisan Command Tests ─────────────────────────────────────

it('darkmode:install command runs with valid options', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertSuccessful();
});

it('darkmode:install command fails with invalid css', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'invalid',
        '--frontend'       => 'blade',
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertFailed();
});

it('darkmode:install command fails with invalid frontend', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'invalid',
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertFailed();
});

// All 15 CSS + Frontend combinations for install
$cssFrameworks = ['tailwind', 'bootstrap5', 'bootstrap4'];
$frontendFrameworks = ['blade', 'livewire', 'vue', 'react', 'svelte'];

foreach ($cssFrameworks as $css) {
    foreach ($frontendFrameworks as $frontend) {
        it("darkmode:install works with {$css} + {$frontend}", function () use ($css, $frontend) {
            $this->artisan('darkmode:install', [
                '--css'            => $css,
                '--frontend'       => $frontend,
                '--force'          => true,
                '--no-interaction' => true,
            ])->assertSuccessful();
        });
    }
}

it('darkmode:switch command switches css framework', function () {
    $this->artisan('darkmode:switch', [
        '--css' => 'bootstrap5',
    ])->assertSuccessful();
});

it('darkmode:switch command switches frontend framework', function () {
    $this->artisan('darkmode:switch', [
        '--frontend' => 'livewire',
    ])->assertSuccessful();
});

it('darkmode:switch command switches both at once', function () {
    $this->artisan('darkmode:switch', [
        '--css'      => 'bootstrap4',
        '--frontend' => 'vue',
    ])->assertSuccessful();
});

it('darkmode:switch fails with no options', function () {
    $this->artisan('darkmode:switch')->assertFailed();
});

it('darkmode:switch fails with invalid css', function () {
    $this->artisan('darkmode:switch', [
        '--css' => 'material',
    ])->assertFailed();
});

it('darkmode:switch fails with invalid frontend', function () {
    $this->artisan('darkmode:switch', [
        '--frontend' => 'angular',
    ])->assertFailed();
});

// All CSS switch combinations
foreach ($cssFrameworks as $css) {
    it("darkmode:switch works with --css={$css}", function () use ($css) {
        $this->artisan('darkmode:switch', ['--css' => $css])->assertSuccessful();
    });
}

// All frontend switch combinations
foreach ($frontendFrameworks as $frontend) {
    it("darkmode:switch works with --frontend={$frontend}", function () use ($frontend) {
        $this->artisan('darkmode:switch', ['--frontend' => $frontend])->assertSuccessful();
    });
}

// All 15 switch combinations
foreach ($cssFrameworks as $css) {
    foreach ($frontendFrameworks as $frontend) {
        it("darkmode:switch works with {$css} + {$frontend}", function () use ($css, $frontend) {
            $this->artisan('darkmode:switch', [
                '--css'      => $css,
                '--frontend' => $frontend,
            ])->assertSuccessful();
        });
    }
}

// ─── Update Command Tests ──────────────────────────────────────

it('darkmode:update runs with valid css option', function () {
    $this->artisan('darkmode:update', [
        '--css' => 'bootstrap5',
    ])->assertSuccessful();
});

it('darkmode:update runs with valid frontend option', function () {
    $this->artisan('darkmode:update', [
        '--frontend' => 'livewire',
    ])->assertSuccessful();
});

it('darkmode:update runs with both options', function () {
    $this->artisan('darkmode:update', [
        '--css'      => 'tailwind',
        '--frontend' => 'vue',
    ])->assertSuccessful();
});

it('darkmode:update fails with invalid css', function () {
    $this->artisan('darkmode:update', [
        '--css' => 'material',
    ])->assertFailed();
});

it('darkmode:update fails with invalid frontend', function () {
    $this->artisan('darkmode:update', [
        '--frontend' => 'angular',
    ])->assertFailed();
});

// All CSS update combinations
foreach ($cssFrameworks as $css) {
    it("darkmode:update works with --css={$css}", function () use ($css) {
        $this->artisan('darkmode:update', ['--css' => $css])->assertSuccessful();
    });
}

// All frontend update combinations
foreach ($frontendFrameworks as $frontend) {
    it("darkmode:update works with --frontend={$frontend}", function () use ($frontend) {
        $this->artisan('darkmode:update', ['--frontend' => $frontend])->assertSuccessful();
    });
}

// All 15 update combinations
foreach ($cssFrameworks as $css) {
    foreach ($frontendFrameworks as $frontend) {
        it("darkmode:update works with {$css} + {$frontend}", function () use ($css, $frontend) {
            $this->artisan('darkmode:update', [
                '--css'      => $css,
                '--frontend' => $frontend,
            ])->assertSuccessful();
        });
    }
}

// ─── Install Already-Installed Detection Tests ─────────────────

it('darkmode:install with --force skips confirmation when already installed', function () {
    $this->artisan('darkmode:install', [
        '--css'            => 'tailwind',
        '--frontend'       => 'blade',
        '--force'          => true,
        '--no-interaction' => true,
    ])->assertSuccessful();
});
