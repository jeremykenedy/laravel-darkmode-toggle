<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures;

class Profile
{
    public ?string $dark_mode = null;

    public ?string $theme_choice = null;

    public function update(array $attributes): bool
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return true;
    }
}
