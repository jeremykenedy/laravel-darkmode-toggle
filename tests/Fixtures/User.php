<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Tests\Fixtures;

use Illuminate\Auth\GenericUser;

/**
 * A user that follows the profile convention the package persists through.
 */
class User extends GenericUser
{
    public ?Profile $profile = null;

    public function __construct(?Profile $profile = null)
    {
        parent::__construct(['id' => 1]);

        $this->profile = $profile;
    }

    public function ensureProfile(): void
    {
        $this->profile ??= new Profile();
    }
}
