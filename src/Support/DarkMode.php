<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Support;

use Jeremykenedy\LaravelDarkmodeToggle\Enums\Mode;

class DarkMode
{
    public const CSS_FRAMEWORKS = ['tailwind', 'bootstrap5', 'bootstrap4'];

    public const FRONTENDS = ['blade', 'livewire', 'vue', 'react', 'svelte'];

    public const CSS_LABELS = [
        'tailwind'   => 'Tailwind CSS v4',
        'bootstrap5' => 'Bootstrap 5',
        'bootstrap4' => 'Bootstrap 4',
    ];

    public const FRONTEND_LABELS = [
        'blade'    => 'Blade + Alpine.js',
        'livewire' => 'Livewire 3',
        'vue'      => 'Vue 3',
        'react'    => 'React 18',
        'svelte'   => 'Svelte 4',
    ];

    public static function cssFramework(): string
    {
        $css = config('darkmode.css_framework') ?? config('ui-kit.css_framework', 'tailwind');

        return self::isValidCssFramework($css) ? $css : 'tailwind';
    }

    public static function frontend(): string
    {
        $frontend = config('darkmode.frontend') ?? config('ui-kit.frontend', 'blade');

        return self::isValidFrontend($frontend) ? $frontend : 'blade';
    }

    public static function isValidCssFramework(mixed $css): bool
    {
        return is_string($css) && in_array($css, self::CSS_FRAMEWORKS, true);
    }

    public static function isValidFrontend(mixed $frontend): bool
    {
        return is_string($frontend) && in_array($frontend, self::FRONTENDS, true);
    }

    public static function prefix(): string
    {
        return self::string('darkmode.prefix', 'darkmode');
    }

    public static function className(): string
    {
        return self::string('darkmode.class_name', 'dark');
    }

    public static function storageKey(): string
    {
        return self::string('darkmode.storage_key', 'theme');
    }

    public static function persistField(): string
    {
        return self::string('darkmode.persist_field', 'dark_mode');
    }

    /**
     * The configured default mode, falling back when it is not a supported mode.
     */
    /**
     * @return array<int, string>
     */
    public static function modes(): array
    {
        return Mode::values();
    }

    public static function defaultMode(): string
    {
        $default = config('darkmode.default', Mode::System->value);

        return Mode::isValid($default) ? $default : Mode::System->value;
    }

    public static function dataAttribute(): ?string
    {
        $attribute = config('darkmode.data_attribute');

        return is_string($attribute) && $attribute !== '' ? $attribute : null;
    }

    /**
     * Read the stored preference off the user's profile, if there is one.
     */
    public static function preferenceFor(mixed $user, ?string $field = null): ?string
    {
        $stored = data_get($user, 'profile.'.($field ?? self::persistField()));

        return Mode::isValid($stored) ? $stored : null;
    }

    /**
     * Write the preference to the user's profile. Applications that do not use
     * the profile convention handle persistence through their own route.
     */
    public static function persistFor(mixed $user, string $mode, ?string $field = null): void
    {
        if (!is_object($user) || !method_exists($user, 'ensureProfile') || !Mode::isValid($mode)) {
            return;
        }

        $user->ensureProfile();

        $profile = data_get($user, 'profile');

        if (is_object($profile) && method_exists($profile, 'update')) {
            $profile->update([$field ?? self::persistField() => $mode]);
        }
    }

    private static function string(string $key, string $fallback): string
    {
        $value = config($key, $fallback);

        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}
