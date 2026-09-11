<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Enums;

enum Mode: string
{
    case Light = 'light';

    case Dark = 'dark';

    case System = 'system';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(mixed $value): bool
    {
        return is_string($value) && self::tryFrom($value) !== null;
    }
}
